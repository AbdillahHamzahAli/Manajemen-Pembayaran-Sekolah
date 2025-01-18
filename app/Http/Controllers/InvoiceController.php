<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi_Iuran;
use App\Models\Transaksi_SPP;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function InvoiceSPP($record)
    {
        $transaksiSPP = Transaksi_SPP::findOrFail($record);
        $pdf = Pdf::loadView('invoices.SPP', compact('transaksiSPP'));
        return $pdf->stream('invoice-' . $transaksiSPP->anggota_kelas->siswa->nis . '.pdf');
        // $invoice = Invoice::findOrFail($id);
        // $pdf = Pdf::loadView('invoice', compact('invoice'));
        // return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }

    public function InvoiceIuran($record)
    {
        $transaksiIuran = Transaksi_Iuran::findOrFail($record);
        $pdf = Pdf::loadView('invoices.iuran', compact('transaksiIuran'));
        return $pdf->stream('invoice-' . $transaksiIuran->anggota_kelas->siswa->nis . '.pdf');
    }
}
