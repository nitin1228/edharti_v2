<?php

namespace App\Services;

use App\Helpers\GeneralFunctions;
use App\Models\Demand;
use App\Models\DemandDetail;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\OldDemand;
use App\Models\OldDemandSubhead;


class PaymentService
{
     public function makePayemnt($payment)
    {
        // dd($payment);
        $xml = '<BharatKoshPayment DepartmentCode="030" Version="1.0"><Submit>';
        $xml .= '<OrderBatch TotalAmount="' . $payment['amount'] . '" Transactions="1" merchantBatchCode="' . $payment['merchant_batch_code'] . '">';
        $xml .= '<Order InstallationId="11141" OrderCode="' . $payment['merchant_batch_code'] . '">';
        $xml .= '<CartDetails><Description/><Amount CurrencyCode="INR" exponent="0" value="' . $payment['amount'] . '"/>';
        $xml .= '<OrderContent>' . $payment['order_content'] . '</OrderContent><PaymentTypeId>0</PaymentTypeId><PAOCode>043884</PAOCode><DDOCode>243896</DDOCode></CartDetails>';
        $xml .= '<PaymentMethodMask><Include Code="'.$payment['code'].'"/></PaymentMethodMask><Shopper><ShopperEmailAddress>' . $payment['email'] . '</ShopperEmailAddress></Shopper>';
        $xml .= '<ShippingAddress><Address><FirstName>' . $payment['first_name'] . '</FirstName><LastName>' . $payment['last_name'] . '</LastName><Address1>' . $payment['address_1'] . '</Address1>' . $payment['address_2'] . '<Address2/><PostalCode>' . $payment['postal_code'] . '</PostalCode><City>' . $payment['city'] . '</City><StateRegion>' . $payment['region'] . '</StateRegion><State>' . $payment['state'] . '</State><CountryCode>' . $payment['country'] . '</CountryCode><MobileNumber>' . $payment['mobile'] . '</MobileNumber></Address></ShippingAddress>';
        $xml .= '<BillingAddress><Address><FirstName>' . $payment['first_name'] . '</FirstName><LastName>' . $payment['last_name'] . '</LastName><Address1>' . $payment['address_1'] . '</Address1>' . $payment['address_2'] . '<Address2/><PostalCode>' . $payment['postal_code'] . '</PostalCode><City>' . $payment['city'] . '</City><StateRegion>' . $payment['region'] . '</StateRegion><State>' . $payment['state'] . '</State><CountryCode>' . $payment['country'] . '</CountryCode><MobileNumber>' . $payment['mobile'] . '</MobileNumber></Address></BillingAddress>';
        $xml .= '<StatementNarrative/><Remarks/></Order></OrderBatch></Submit></BharatKoshPayment>';
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = true;
        $dom->loadXML($xml);
        $simpleFilePath = Storage::disk('public')->path('NTRP/xmls/simple/simple' . date("Ymdhis") . '.xml');
        $signedFilePath = Storage::disk('public')->path('NTRP/xmls/signed/signed' . date("Ymdhis") . '.xml');
        // Save the file
        $dom->save($simpleFilePath);

 
        $X509certificate = file_get_contents(Storage::disk('public')->path('NTRP/certificate.pem'));
        $privatePemFile = file_get_contents(Storage::disk('public')->path('NTRP/private-key-encrypted.pem'));

        // Provide the passphrase as the second argument
        $pkeyid = openssl_pkey_get_private($privatePemFile, '12345678');

        if (!$pkeyid) {
            die("Failed to load private key.");
        }
        // $pkeyid = openssl_pkey_get_private($private_key);
        // $details = openssl_pkey_get_details($pkeyid);
        $X509 = openssl_x509_parse($X509certificate);
        $publicCertificatePureString = str_replace('-----BEGIN CERTIFICATE-----', '', $X509certificate);
        $publicCertificatePureString = str_replace('-----END CERTIFICATE-----', '', $publicCertificatePureString);
        $publicCertificatePureString = preg_replace("/\r|\n/", "", $publicCertificatePureString);

        $X509Issuer = $X509['issuer']['CN'];
        $X509IssuerName = 'CN=' . $X509Issuer;
        $X509issuerserial = $X509['serialNumber'];

        $XMLRequestDOMDoc = new \DOMDocument();
        $XMLRequestDOMDoc->preserveWhiteSpace = true;
        $XMLRequestDOMDoc->load($simpleFilePath);
       
        $canonical = $XMLRequestDOMDoc->C14N();
        $DigestValue = base64_encode(hash('sha1', $canonical, true));

        $rootElem = $XMLRequestDOMDoc->documentElement;

        $SignatureNode = $rootElem->appendChild(new \DOMElement('Signature'));
        $SignatureNode->setAttribute('xmlns', 'http://www.w3.org/2000/09/xmldsig#');

        $SignedInfoNode = $SignatureNode->appendChild(new \DOMElement('SignedInfo'));

        $CanonicalizationMethodNode = $SignedInfoNode->appendChild(new \DOMElement('CanonicalizationMethod'));
        $CanonicalizationMethodNode->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');

        $SignatureMethodNode = $SignedInfoNode->appendChild(new \DOMElement('SignatureMethod'));
        $SignatureMethodNode->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');

        $ReferenceNode = $SignedInfoNode->appendChild(new \DOMElement('Reference'));
        $ReferenceNode->setAttribute('URI', '');

        $TransformsNode = $ReferenceNode->appendChild(new \DOMElement('Transforms'));

        $Transform1Node = $TransformsNode->appendChild(new \DOMElement('Transform'));
        $Transform1Node->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');

        $DigestMethodNode = $ReferenceNode->appendChild(new \DOMElement('DigestMethod'));
        $DigestMethodNode->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');

        $ReferenceNode->appendChild(new \DOMElement('DigestValue', $DigestValue));

        $SignedInfoNode = $XMLRequestDOMDoc->getElementsByTagName('SignedInfo')->item(0);

        if (!openssl_sign($SignedInfoNode->C14N(), $signature, $pkeyid, OPENSSL_ALGO_SHA1)) {
            throw new \Exception('Unable to sign the request');
        }

        $SignatureNode = $XMLRequestDOMDoc->getElementsByTagName('Signature')->item(0);
        $SignatureValueNode = new \DOMElement('SignatureValue', base64_encode($signature));
        $SignatureNode->appendChild($SignatureValueNode);

        $KeyInfoNode = $SignatureNode->appendChild(new \DOMElement('KeyInfo'));

        $X509DataNode = $KeyInfoNode->appendChild(new \DOMElement('X509Data'));
        $X509IssuerSerialNode = $X509DataNode->appendChild(new \DOMElement('X509IssuerSerial'));

        $X509IssuerNameNode = new \DOMElement('X509IssuerName', $X509IssuerName);
        $X509IssuerSerialNode->appendChild($X509IssuerNameNode);

        $X509SerialNumberNode = new \DOMElement('X509SerialNumber', $X509issuerserial);
        $X509IssuerSerialNode->appendChild($X509SerialNumberNode);

        $X509CertificateNode = new \DOMElement('X509Certificate', $publicCertificatePureString);
        $X509DataNode->appendChild($X509CertificateNode);

        // Save the signed xml file
        file_put_contents($signedFilePath, $XMLRequestDOMDoc->saveXML());
        $data = file_get_contents(($signedFilePath));
        $base64 = base64_encode($data);
        $paytype = $payment['code'] ;
        $paycode = $payment['order_code'];
        /** update payemnt data - add base64 in row */
        $payment = Payment::where('unique_payment_id', $payment['order_code'])->first();
        if (!empty($payment)) {
            $payment->update([
                'request' => $base64
            ]);
        }
        // dd($base64);
        $url = config('constants.paymentURL');
//$url = "https://www.google.com";       
//  echo '<form id="postForm" action="' . $url . '" method="POST"><input type="hidden" name="bharrkkosh"value= "' . $base64 . '">
//             <h2>Redirecting to payment gateway</h2>
//         </form>';
//         // Automatically submit the form using JavaScript
//         echo '<script type="text/javascript">
//         setTimeout(function(){
//             document.getElementById("postForm").submit();
//         }, 2000);
//       </script>';
//dd($payment['code']."test".$payment['order_code']);
if($paytype == 'OffLine'){
		echo '
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<div class="modal fade" id="offlineModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title w-100 text-center">
          Message from Online Payment
        </h5>
      </div>

      <div class="modal-body text-center">
        <p>Please save this Payment ID for future correspondence and payment status verification.</p>
        <p><strong>'. $paycode .'</strong></p>
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-success px-4" id="confirmBtn">
          Pay Now
        </button>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {   
console.log("test")
    setTimeout(function () {
        var modalEl = document.getElementById("offlineModal");

        if (modalEl) {
            var myModal = new bootstrap.Modal(modalEl);
            myModal.show();
        }
    }, 2000); 
    var btn = document.getElementById("confirmBtn");
    if (btn) {
        btn.addEventListener("click", function () {

            var form = document.createElement("form");
            form.method = "POST";
            form.action = "'.$url.'";

            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "bharrkkosh";
            input.value = "'.$base64.'";

            form.appendChild(input);
            document.body.appendChild(form);

            form.submit();
        });
    }

});
</script>';
				}
				else
				{
			    echo '<form id="postForm" action="' . $url . '" method="POST">
			            <input type="hidden" name="bharrkkosh" value= "'. $base64 .'">
                          <h2>Redirecting to payment gateway</h2>
			        </form>';

			         echo '<script type="text/javascript">
					        setTimeout(function(){
					            document.getElementById("postForm").submit();
					        }, 2000);
					      </script>';
				}
        $orderId = '';
        $purpose = '';

        // $url = "https://bharatkosh.gov.in/getstatus";
       /* $url = "http://164.100.129.32/bharatkosh/getstatus";
        $data = array("OrderId" => $orderId, "PurposeId" => $purpose);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            $curl_output = curl_exec($ch);
            $response_api = explode('|', $curl_output);
            curl_close($ch);
            if (count($response_api) > 1) {
            } else {
                return $response_api;
            }
        }
*/
    }


    // public function processSuccessfulPayment($paymentRecord)
    // {
    //     // dd($paymentRecord);
    //     $payemntType = getServiceCodeById($paymentRecord->type);
    //     if ($payemntType == "PAY_DEMAND") {
    //         if (is_null($paymentRecord->demand_id)) {
    //             return;
    //         }

    //         // Start a database transaction for consistency
    //         DB::transaction(function () use ($paymentRecord) {
    //             $demand = Demand::find($paymentRecord->demand_id);

    //             if ($demand) {
    //                 $this->updateDemand($demand, $paymentRecord->amount);

    //                 // Process payment details for subheads
    //                 foreach ($paymentRecord->paymentDetails as $paydetail) {
    //                     $demandSubhead = DemandDetail::find($paydetail->subhead_id);
    //                     if ($demandSubhead) {
    //                         $this->updateDemandSubhead($demandSubhead, $paydetail->paid_amount);
    //                     }
    //                 }
    //             }
    //         });
    //     }
    //     if ($payemntType == "PAY_APP_CHG") {
    //         $submitted = GeneralFunctions::convertTempAppToFinal($paymentRecord->model_id, $paymentRecord->model, $paymentRecord);
    //     }
    // }
    public function processSuccessfulPayment($paymentRecord)
    {
         //dd($paymentRecord->type);
         if($paymentRecord->demand_type ==1)
         {
         	    $payemntType = getServiceCodeById($paymentRecord->type);
		        if ($payemntType == "PAY_DEMAND") {
		            if (is_null($paymentRecord->demand_id)) {
		                return;
		            }

		            // Start a database transaction for consistency
		            DB::transaction(function () use ($paymentRecord) {
		               $demand = OldDemand::where('demand_id', $paymentRecord->demand_id)->first();

		                if ($demand) {
		                    $this->updateDemandOld($demand, $paymentRecord->amount);

		                    // Process payment details for subheads
//		                    foreach ($paymentRecord->paymentDetails as $paydetail) {
//		                        $demandSubhead = DemandDetail::find($paydetail->subhead_id);
//		                        if ($demandSubhead) {
//		                            $this->updateDemandSubhead($demandSubhead, $paydetail->paid_amount);
//		                        }
//		                    }
		                }
		            });
		        }
         }
         else
         {
        $payemntType = getServiceCodeById($paymentRecord->type);
        if ($payemntType == "PAY_DEMAND") {
            if (is_null($paymentRecord->demand_id)) {
                return;
            }

            // Start a database transaction for consistency
            DB::transaction(function () use ($paymentRecord) {
                $demand = Demand::find($paymentRecord->demand_id);

                if ($demand) {
                    $this->updateDemand($demand, $paymentRecord->amount);

                    // Process payment details for subheads
                    foreach ($paymentRecord->paymentDetails as $paydetail) {
                        $demandSubhead = DemandDetail::find($paydetail->subhead_id);
                        if ($demandSubhead) {
                            $this->updateDemandSubhead($demandSubhead, $paydetail->paid_amount);
                        }
                    }
                }
            });
        }
	}
        if ($payemntType == "PAY_APP_CHG") {
            $submitted = GeneralFunctions::convertTempAppToFinal($paymentRecord->model_id, $paymentRecord->model, $paymentRecord);
        }
    }

    private function updateDemandOld($demand, $paymentAmount)
    {
        $updatedStatus = $paymentAmount >= $demand->outstanding
            ? getServiceType('DEM_PAID')
            : getServiceType('DEM_PART_PAID');

        $demand->update([
            'paid_amount' => ($demand->paid_amount ?? 0) + $paymentAmount,
            'outstanding' => $demand->outstanding - $paymentAmount,
            'pstatus' => $updatedStatus,
            'status'=> 1,
        ]);
        // dd($demand);
    }


    private function updateDemand($demand, $paymentAmount)
    {
        $updatedStatus = $paymentAmount >= $demand->balance_amount
            ? getServiceType('DEM_PAID')
            : getServiceType('DEM_PART_PAID');

        $demand->update([
            'paid_amount' => ($demand->paid_amount ?? 0) + $paymentAmount,
            'balance_amount' => $demand->balance_amount - $paymentAmount,
            'status' => $updatedStatus,
        ]);
        // dd($demand);
    }

    private function updateDemandSubhead($demandSubhead, $paidAmount)
    {

        $demandSubhead->update([
            'paid_amount' => ($demandSubhead->paid_amount ?? 0) + $paidAmount,
            'balance_amount' => $demandSubhead->balance_amount - $paidAmount,
        ]);
    }

    // This is main cron to update demand & payment as well
    public function checkUpdatedPaymentStatus($paymentId)
    {
        // dd($paymentId);
        $payment = Payment::find($paymentId);
        $orderId = $payment->unique_payment_id;
        $purposeId = '15777'; //23092
        $url = config('constants.paymentStatusURL');
        $data = array("OrderId" => $orderId, "PurposeId" => $purposeId);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            $curl_output = trim(curl_exec($ch), '"');
            $apiResponse = explode('|', $curl_output);
            curl_close($ch);
            if (count($apiResponse) > 1) {
                list($orderId, $orderStatus, $transactionId) = $apiResponse;
                
                if (strtoupper($orderStatus) == "SUCCESS") {
                    /* $paymentData = Payment::where('unique_payment_id', $orderId)->first();
                    if (!empty($paymentData)) {
                        $this->processSuccessfulPayment($paymentData);
                    } */
                    $paymentData = Payment::where('unique_payment_id', $orderId)->first();
                            if (!empty($paymentData)) {
                                if ($paymentData->status == getServiceType('PAY_SUCCESS')) {
					        		return;
					   				 }
                                $paymentData->update([
                                    'status' => getServiceType('PAY_' . $orderStatus) ?? getServiceType('PAY_PENDING') ,
                                    'response' => $curl_output,
                                    'transaction_id' => $transactionId,
                                ]);

                                // dd($paymentData);

                                $paymentService = new PaymentService();
                                $paymentService->processSuccessfulPayment($paymentData);
                        }
                } else {
                    if (strtoupper($orderStatus) == "PENDING") {
                        $payment->update(
                            ['status' => getServiceType('PAY_PENDING')]
                        );
                    }
                }
                Log::info('payment status checked', [
                    'payment id' => $payment->unique_payment_id,
                    'status' => $payment->status == 1 ? 1 :getServiceNameById($payment->status)
                ]);
            } else {
            }
        }
    }
}
