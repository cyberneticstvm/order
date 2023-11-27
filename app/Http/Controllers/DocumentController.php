<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Patient;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    function __construct(){
        $this->middleware('permission:document-list|document-create|document-delete', ['only' => ['index','store']]);
        $this->middleware('permission:document-create', ['only' => ['fetch', 'show', 'store']]);
        $this->middleware('permission:document-delete', ['only' => ['destroy']]);
    }

    public function index(){
       return view('backend.document.index');
    }

    public function fetch(Request $request){
        $this->validate($request, [
            'patient_id' => 'required',
        ]);
        $patient = Patient::findOrFail($request->patient_id);
        return view('backend.document.proceed', compact('patient'));
    }

    public function show($id){
        $patient = Patient::findOrFail(decrypt($id));
        $docs = Document::with('consultation')->where('patient_id', $patient->id)->latest()->get();
        return view('backend.document.create', compact('patient', 'docs'));
    }

    public function store(Request $request){
        $this->validate($request, [
            'documents' => 'required',
            'mrn' => 'required',
            'description' => 'required',
        ]);
        $documents = $request->file('documents');
        foreach($documents as $key => $item):
            $url = uploadDocument($item, $path = 'patient/'.$request->patient_id);
            Document::create([
                'patient_id' => $request->patient_id,
                'consultation_id' => $request->mrn,
                'document_url' => $url,
                'original_file_name' => $item->getclientOriginalName(),
                'description' => $request->description,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);
        endforeach;
        return redirect()->back()
                        ->with('success','Document uploaded successfully!');
    }

    public function destroy($id){
        $document = Document::findOrFail(decrypt($id));
        //deleteDocument($path = 'patient/'.$document->patient_id.'/', $document->document_url);
        $document->delete();
        return redirect()->back()
                        ->with('success','Document deleted successfully!');
    }
}
