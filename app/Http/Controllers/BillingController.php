<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\CustomField;
use App\Models\Invoice;
use App\Models\InvoiceReturn;

use App\Models\InvoicePayment;
use App\Models\InvoiceProduct;
use App\Models\InvoiceReturnProduct;

use App\Models\Mail\CustomerInvoiceSend;
use App\Models\Mail\InvoicePaymentCreate;
use App\Models\Mail\InvoiceSend;
use App\Models\Mail\PaymentReminder;
use App\Models\Milestone;
use App\Models\Products;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\StockReport;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\Utility;
use App\Models\User;
use App\Models\Voucher;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoiceExport;
use Carbon\Carbon;



class BillingController extends Controller
{
    // public function customerlist(Request $request){

    //     return view('customerbill.customer_list');
    // }
}
