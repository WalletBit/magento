<?php
	// callback controller
	class WalletBit_Bitcoins_IndexController extends Mage_Core_Controller_Front_Action {
	
		// walletbit's IPN lands here
		public function indexAction()
		{
			$str =
			$_POST["merchant"].":".
			$_POST["customer_email"].":".
			$_POST["amount"].":".
			$_POST["batchnumber"].":".
			$_POST["txid"].":".
			$_POST["address"].":".
			Mage::getStoreConfig('payment/Bitcoins/walletbit_securityword');

			$hash = strtoupper(hash('sha256', $str));

			// proccessing payment only if hash is valid
			if ($_POST["merchant"] == Mage::getStoreConfig('payment/Bitcoins/walletbit_email') && $_POST["encrypted"] == $hash && $_POST["status"] == 1)
			{
				print '1';

				// get the order
				if (isset($_POST['quoteId']))
				{
					$quoteId = $_POST['quoteId'];
					$order = Mage::getModel('sales/order')->load($quoteId, 'quote_id');
				}
				else
				{
					$orderId = $_POST['orderId'];
					$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
				}

				// save the ipn so that we can find it when the user clicks "Place Order"
				Mage::getModel('Bitcoins/ipn')->Record($_POST);

				// update the order if it exists already
				if ($order->getId())
					switch($_POST['status']) {
					case '1':
						foreach($order->getInvoiceCollection() as $i)
							$i->pay()->save();
					
						$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
						break;
					}
			}
			else
			{
				Mage::log("walletbit callback error: " . $_POST["batchnumber"]);
			}
		}
	}
?>