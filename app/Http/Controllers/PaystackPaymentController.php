<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Retainer;
use App\Models\RetainerPayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PaystackPaymentController extends Controller
{
    //
    public $secret_key;
    public $public_key;
    public $is_enabled;



    public function paymentConfig()
    {
        $user = \Auth::user();

        $payment_setting = Utility::getCompanyPaymentSetting();

        $this->secret_key = isset($payment_setting['paystack_secret_key']) ? $payment_setting['paystack_secret_key'] : '';
        $this->public_key = isset($payment_setting['paystack_public_key']) ? $payment_setting['paystack_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_paystack_enabled']) ? $payment_setting['is_paystack_enabled'] : 'off';
    }


    public function non_auth_paymentConfig($id)
    {

        $payment_setting = Utility::getNonAuthCompanyPaymentSetting($id);
        $this->secret_key = isset($payment_setting['paystack_secret_key']) ? $payment_setting['paystack_secret_key'] : '';
        $this->public_key = isset($payment_setting['paystack_public_key']) ? $payment_setting['paystack_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_paystack_enabled']) ? $payment_setting['is_paystack_enabled'] : 'off';
    }


    public function invoicePayWithPaystack(Request $request)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice   = Invoice::find($invoiceID);

        if ($invoice) {
            $price = $request->amount;
            if ($price > 0) {
                $res_data['email']       = $invoice->customer->email;
                $res_data['total_price'] = (int)$price;
                $res_data['currency']    = Utility::getValByName('site_currency');
                $res_data['flag']        = 1;

                return $res_data;
            } else {
                $res['msg']  = __("Enter valid amount.");
                $res['flag'] = 2;

                return $res;
            }
        } else {
            return redirect()->route('invoice.index')->with('error', __('Invoice is deleted.'));
        }
    }

    public function getInvoicePaymentStatus(Request $request, $pay_id, $invoice_id)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
        $invoice   = Invoice::find($invoiceID);

        if (Auth::check()) {
            $payment  = $this->paymentConfig();
            $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
        } else {
            $payment_setting = Utility::getNonAuthCompanyPaymentSetting($invoice->created_by);
            $this->secret_key = isset($payment_setting['paystack_secret_key']) ? $payment_setting['paystack_secret_key'] : '';
            $this->public_key = isset($payment_setting['paystack_public_key']) ? $payment_setting['paystack_public_key'] : '';
            $this->is_enabled = isset($payment_setting['is_paystack_enabled']) ? $payment_setting['is_paystack_enabled'] : 'off';
            $settings = Utility::settingsById($invoice->created_by);
        }
        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
        $result    = array();

        if ($invoice) {
            try {

                //The parameter after verify/ is the transaction reference to be verified
                $url = "https://api.paystack.co/transaction/verify/$pay_id";
                $ch  = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    [
                        'Authorization: Bearer ' . $this->secret_key,
                    ]
                );
                $responce = curl_exec($ch);
                curl_close($ch);
                if ($responce) {
                    $result = json_decode($responce, true);
                }

                if (isset($result['status']) && $result['status'] == true) {

                    $payments = InvoicePayment::create(
                        [
                            'invoice_id' => $invoice->id,
                            'date' => date('Y-m-d'),
                            'amount' => $request->amount,
                            'payment_method' => 1,
                            'order_id' => $orderID,
                            'payment_type' => __('Paystack'),
                            'receipt' => '',
                            'description' => __('Invoice') . ' ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),

                        ]
                    );

                    $invoice = Invoice::find($invoice->id);

                    if ($invoice->getDue() <= 0.0) {
                        Invoice::change_status($invoice->id, 4);
                    } elseif ($invoice->getDue() > 0) {
                        Invoice::change_status($invoice->id, 3);
                    } else {
                        Invoice::change_status($invoice->id, 2);
                    }

                    //Twilio Notification
                    if (Auth::check()) {
                        $setting  = Utility::settings(\Auth::user()->creatorId());
                    }
                    $customer = Customer::find($invoice->customer_id);
                    if(isset($setting['payment_notification']) && $setting['payment_notification'] ==1)
                    {
                        $msg = __("New payment of").' ' . $request->amount . __("created for").' ' . $customer->name . __("by").' '.  $payments['payment_type'] . '.';
                        Utility::send_twilio_msg($customer->contact,$msg);
                    }

                    if (Auth::check()) {
                        return redirect()->route('invoice.show', \Crypt::encrypt($invoice->id))->with('success', __('Payment successfully added.'));
                    } else {
                        return redirect()->back()->with('success', __(' Payment successfully added.'));
                    }
                } else {
                    if (Auth::check()) {
                        return redirect()->route('invoice.show', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been ' . $status));
                    } else {
                        return redirect()->route('pay.invoice', encrypt($invoice->id))->with('error', __('Transaction Unsuccesfull'));
                    }
                }
            } catch (\Exception $e) {
                if (Auth::check()) {
                    return redirect()->route('invoice.show', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                }
            }
        } else {
            return redirect()->route('pay.invoice', encrypt($invoice->id))->with('error', __('Invoice is deleted.'));
        }
    }

    public function RetainerPayWithPaystack(Request $request)
    {

        $retainerID = \Illuminate\Support\Facades\Crypt::decrypt($request->retainer_id);
        $retainer   = Retainer::find($retainerID);

        if ($retainer) {
            $price = $request->amount;
            
            if ($price > 0) {
                $res_data['email']       = $retainer->customer->email;
                $res_data['total_price'] = (int)$price;
                $res_data['currency']    = !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD';
                $res_data['flag']        = 1;
                
                return $res_data;
            } else {
                $res['msg']  = __("Enter valid amount.");
                $res['flag'] = 2;

                return $res;
            }
        } else {
            return redirect()->route('customer.retainer')->with('error', __('Invoice is deleted.'));
        }
    }

    public function getRetainerPaymentStatus(Request $request, $pay_id, $invoice_id)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
        $invoice   = Invoice::find($invoiceID);

        if (Auth::check()) {
            $payment  = $this->paymentConfig();
            $settings = DB::table('settings')->where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('value', 'name');
        } else {
            $payment_setting = Utility::getNonAuthCompanyPaymentSetting($invoice->created_by);
            $this->secret_key = isset($payment_setting['paystack_secret_key']) ? $payment_setting['paystack_secret_key'] : '';
            $this->public_key = isset($payment_setting['paystack_public_key']) ? $payment_setting['paystack_public_key'] : '';
            $this->is_enabled = isset($payment_setting['is_paystack_enabled']) ? $payment_setting['is_paystack_enabled'] : 'off';
            $settings = Utility::settingsById($invoice->created_by);
        }
        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
        $result    = array();

        if ($invoice) {
            try {

                //The parameter after verify/ is the transaction reference to be verified
                $url = "https://api.paystack.co/transaction/verify/$pay_id";
                $ch  = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    [
                        'Authorization: Bearer ' . $this->secret_key,
                    ]
                );
                $responce = curl_exec($ch);
                curl_close($ch);
                if ($responce) {
                    $result = json_decode($responce, true);
                }

                if (isset($result['status']) && $result['status'] == true) {

                    $payments = InvoicePayment::create(
                        [
                            'invoice_id' => $invoice->id,
                            'date' => date('Y-m-d'),
                            'amount' => $request->amount,
                            'payment_method' => 1,
                            'order_id' => $orderID,
                            'payment_type' => __('Paystack'),
                            'receipt' => '',
                            'description' => __('Invoice') . ' ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),

                        ]
                    );

                    $invoice = Invoice::find($invoice->id);

                    if ($invoice->getDue() <= 0.0) {
                        Invoice::change_status($invoice->id, 4);
                    } elseif ($invoice->getDue() > 0) {
                        Invoice::change_status($invoice->id, 3);
                    } else {
                        Invoice::change_status($invoice->id, 2);
                    }

                    //Twilio Notification
                    if (Auth::check()) {
                        $setting  = Utility::settings(\Auth::user()->creatorId());
                    }
                   

                    $customer = Customer::find($invoice->customer_id);
                    if(isset($setting['payment_notification']) && $setting['payment_notification'] ==1)
                    {
                        $msg = __("New payment of").' ' . $request->amount . __("created for").' ' . $customer->name . __("by").' '.  $payments['payment_type'] . '.';
                        Utility::send_twilio_msg($customer->contact,$msg);
                    }


                    if (Auth::check()) {
                        return redirect()->route('invoice.show', \Crypt::encrypt($invoice->id))->with('success', __('Payment successfully added.'));
                    } else {
                        return redirect()->back()->with('success', __(' Payment successfully added.'));
                    }
                } else {
                    if (Auth::check()) {
                        return redirect()->route('invoice.show', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been ' . $status));
                    } else {
                        return redirect()->back()->with('error', __('Transaction succesfull'));
                    }
                }
            } catch (\Exception $e) {
                if (Auth::check()) {
                    return redirect()->route('invoice.show', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                } else {
                    return redirect()->back()->with('error', __('Transaction has been complted.'));
                }
            }
        } else {
            return redirect()->route('pay.invoice', encrypt($invoice->id))->with('error', __('Invoice is deleted.'));
        }
    }
}
