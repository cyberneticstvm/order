<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = ['order_date' => 'datetime', 'expected_delivery_date' => 'datetime', 'invoice_generated_at' => 'datetime'];

    public function status()
    {
        return ($this->deleted_at) ? "<span class='badge badge-danger'>Deleted</span>" : "<span class='badge badge-success'>Active</span>";
    }

    public function ono()
    {
        return $this->branch->code . '/' . $this->id;
    }

    public function ino()
    {
        return ($this->invoice_number) ? 'INV/' . $this->branch->code . '/' . $this->order_sequence : '';
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'id')->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->withTrashed();
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function history()
    {
        return $this->hasMany(OrderHistory::class, 'order_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }

    public function adviser()
    {
        return $this->belongsTo(User::class, 'product_adviser', 'id')->withTrashed();
    }

    public function isEdited()
    {
        $txt = "";
        if ($this->prescription_updated_at) :
            $txt = "text-danger fw-bold";
        elseif ($this->created_at != $this->updated_at) :
            $txt = "text-warning fw-bold";
        endif;
        return $txt;
    }

    public function labNotes()
    {
        return $this->hasMany(LabOrderNote::class, 'order_id', 'id');
    }

    public function frameType($oid)
    {
        $frame = OrderDetail::where('order_id', $oid)->where('eye', 'frame')->first();
        $product = Product::find($frame->product_id);
        $type = ProductSubcategory::where('id', $product->type_id)->first();
        return $type->name ?? null;
    }
}
