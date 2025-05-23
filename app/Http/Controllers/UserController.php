<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LoginLog;
use App\Models\Order;
use App\Models\Patient;
use App\Models\User;
use App\Models\UserBranch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function wa()
    {
        //$order = Order::findOrFail(36);
        //$res = sendWAMessage($order, 'order');
        //$res = sendWAMessageWithLink($order, 'receipt');
        //dd($res);
        //die;
    }

    public function login()
    {
        return view('backend.login');
    }

    public function signin(Request $request)
    {
        $cred = $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
        if (!$request->place_id):
            return redirect()->back()->with("error", "Denied!!! You dont have enabled an active location. Please enable your location and try again.");
        else:
            Cookie::forget('location');
            Cookie::queue('location', $request->place_id, time() + 60 * 60 * 24 * 365);
        endif;
        if (Auth::attempt($cred, $request->remember)) :
            if (Str::contains($request->userAgent(), ['iPhone', 'Android', 'Linux', 'Macintosh']) && !Auth::user()->mobile_access) :
                Auth::logout();
                return redirect()->route('login')->with("error", "Mobile access has been restricted for this login");
            endif;
            if (settings()->enable_ip_info == 1)
                $this->loginLog($request);
            Session::put('uagent', $request->userAgent());
            return redirect()->route('dashboard')->withSuccess(Auth::user()->name . " logged in successfully!");
        endif;
        return redirect()->route('login')
            ->withError('Invalid Credentials!')->withInput($request->all());
    }

    public function loginLog($request)
    {
        $sid = Str::random(25);
        $ip = ($request->ip() == '127.0.0.1') ? '59.89.235.2' : $request->ip();
        //$data = file_get_contents("https://ipinfo.io/$ip?token=38fa67afac8600");
        //$obj = json_decode($data);
        //$coordinates = explode(",", $obj->loc);
        User::where('id', Auth::id())->update(['session_id' => $sid]);
        LoginLog::create([
            'user_id' => Auth::id(),
            'session_id' => $sid,
            'ip' => $request->ip(),
            'device' => Str::contains($request->userAgent(), ['iPhone', 'Android']) ? 'Mobile' : 'Computer',
            'address' => $request->address,
            'place_id' => $request->place_id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'logged_in' => Carbon::now()
        ]);
    }

    public function dashboard()
    {
        $patients = Patient::whereDate('created_at', Carbon::today())->where('branch_id', Session::get('branch'))->withTrashed()->latest()->get();
        $branches = Branch::whereIn('id', UserBranch::where('user_id', Auth::id())->pluck('branch_id'))->pluck('name', 'id');
        $dvals = array('0' => '0.00', '1' => '0.00', '2' => '0.00');
        if (Session::has('branch')) :
            $branch = Branch::findOrFail(Session::get('branch'));
            $unpaid = unpaidTotal($branch->id, $month = 0, $year = 0,  0);
            $target = ($branch->target_percentage > 0) ? $branch->monthly_target + (($branch->monthly_target * $branch->target_percentage) / 100) : $branch->monthly_target;
            $dvals[0] = $target;
            $dvals[1] = Order::where('branch_id', Session::get('branch'))->whereBetween('invoice_generated_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->where('order_status', 'delivered')->sum('invoice_total');
            $dvals[2] = $unpaid->balance;
        endif;
        $uagent = Session::get('uagent');
        return view('backend.dashboard', compact('branches', 'patients', 'dvals', 'uagent'));
    }

    public function updateBranch(Request $request)
    {
        Session::put('branch', $request->branch);
        if (Session::has('branch')) :
            return redirect()->route('dashboard')
                ->withSuccess('User branch updated successfully!');
        else :
            return redirect()->route('dashboard')
                ->withError('Please update branch!');
        endif;
    }

    public function logout(Request $request)
    {
        LoginLog::where('user_id', $request->user()->id)->where('session_id', $request->user()->session_id)->update([
            'logged_out' => Carbon::now(),
        ]);
        User::where('id', $request->user()->id)->update(['session_id' => null]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('User logged out successfully!');
    }

    public function index()
    {
        $users = User::withTrashed()->latest()->get();
        return view('backend.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        $branches = Branch::pluck('name', 'id');
        return view('backend.user.create', compact('roles', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|numeric|digits:10',
            'password' => 'required|confirmed',
            'roles' => 'required',
            'branches' => 'required',
            'mobile_access' => 'required',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
        $data = [];
        foreach ($request->branches as $key => $br) :
            $data[] = [
                'user_id' => $user->id,
                'branch_id' => $br,
            ];
        endforeach;
        UserBranch::insert($data);
        return redirect()->route('users')
            ->with('success', 'User has been created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail(decrypt($id));
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        $branches = UserBranch::where('user_id', decrypt($id))->get();
        return view('backend.user.edit', compact('user', 'roles', 'userRole', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'mobile' => 'required|numeric|digits:10',
            'roles' => 'required',
            'branches' => 'required',
            'mobile_access' => 'required',
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $user = User::findOrFail($id);
        $user->update($input);
        $data = [];
        foreach ($request->branches as $key => $br) :
            $data[] = [
                'user_id' => $user->id,
                'branch_id' => $br,
            ];
        endforeach;
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        UserBranch::where('user_id', $id)->delete();
        UserBranch::insert($data);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users')
            ->with('success', 'User has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::findOrFail(decrypt($id))->delete();
        return redirect()->route('users')
            ->with('success', 'User deleted successfully');
    }

    public function changePwd()
    {
        return view('backend.user.change-pwd');
    }

    public function updatePwd(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|confirmed',
        ]);
        User::findOrFail($request->user()->id)->update([
            'password' => Hash::make($request->password),
        ]);
        return redirect()->back()
            ->with('success', 'Password has been updated successfully');
    }
}
