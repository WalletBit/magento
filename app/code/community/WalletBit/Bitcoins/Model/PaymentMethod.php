<?php
	/**
	* Our test CC module adapter
	*/
	class WalletBit_Bitcoins_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
	{
		/**
		* unique internal payment method identifier
		*
		* @var string [a-z0-9_]
		*/
		protected $_code = 'Bitcoins';
	 
		/**
		 * Here are examples of flags that will determine functionality availability
		 * of this module to be used by frontend and backend.
		 *
		 * @see all flags and their defaults in Mage_Payment_Model_Method_Abstract
		 *
		 * It is possible to have a custom dynamic logic by overloading
		 * public function can* for each flag respectively
		 */
		 
		/**
		 * Is this payment method a gateway (online auth/charge) ?
		 */
		protected $_isGateway               = true;
	 
		/**
		 * Can authorize online?
		 */
		protected $_canAuthorize            = true;
	 
		/**
		 * Can capture funds online?
		 */
		protected $_canCapture              = false;
	 
		/**
		 * Can capture partial amounts online?
		 */
		protected $_canCapturePartial       = false;
	 
		/**
		 * Can refund online?
		 */
		protected $_canRefund               = false;
	 
		/**
		 * Can void transactions online?
		 */
		protected $_canVoid                 = false;
	 
		/**
		 * Can use this payment method in administration panel?
		 */
		protected $_canUseInternal          = false;
	 
		/**
		 * Can show this payment method as an option on checkout payment page?
		 */
		protected $_canUseCheckout          = true;
	 
		/**
		 * Is this payment method suitable for multi-shipping checkout?
		 */
		protected $_canUseForMultishipping  = true;
	 
		/**
		 * Can save credit card information for future processing?
		 */
		protected $_canSaveCc = false;
		
		//protected $_formBlockType = 'bitcoins/form';
		//protected $_infoBlockType = 'bitcoins/info';
		
		public function canUseCheckout()
		{
			$walletbit_email = Mage::getStoreConfig('payment/Bitcoins/walletbit_email');
			if (!$walletbit_email or !strlen($walletbit_email))
			{
				Mage::log('WalletBit/Bitcoins: Email not entered');
				return false;
			}
			
			$walletbit_token = Mage::getStoreConfig('payment/Bitcoins/walletbit_token');
			if (!$walletbit_token or !strlen($walletbit_token))
			{
				Mage::log('WalletBit/Bitcoins: Token key not entered');
				return false;
			}

			$walletbit_securityword = Mage::getStoreConfig('payment/Bitcoins/walletbit_securityword');
			if (!$walletbit_securityword or !strlen($walletbit_securityword))
			{
				Mage::log('WalletBit/Bitcoins: Security word not entered');
				return false;
			}
			
			return $this->_canUseCheckout;
		}

		public function authorize(Varien_Object $payment, $amount) 
		{
			if (!Mage::getStoreConfig('payment/Bitcoins/onsite'))
				return $this->CreateInvoiceAndRedirect($payment, $amount);
			else
				return $this->CheckForPayment($payment);
		}
		
		function CheckForPayment($payment)
		{
			$quoteId = $payment->getOrder()->getQuoteId();
			$ipn = Mage::getModel('Bitcoins/ipn');
			if (!$ipn->GetQuotePaid($quoteId))
			{
				Mage::throwException("Order not paid for. Please pay first and then Place your Order.");
			}
			
			return $this;
		}
		
		function CreateInvoiceAndRedirect($payment, $amount)
		{
			$order = $payment->getOrder();
			$orderId = $order->getIncrementId();  

			Mage::getSingleton('core/session', array('name'=>'frontend'));
			$session = Mage::getSingleton('checkout/session');

			$item_name = '';

			foreach ($session->getQuote()->getAllItems() as $item)
			{
				$item_name .= 'SKU:' . $item->getSku() . ', ';
				$item_name .= $item->getName() . ', ';
				$item_name .= 'Qty:' . $item->getQty() . ' - ';
			}

			// Mage::getUrl('walletbit_callback')

			$url = 'https://walletbit.com/pay?token=' . Mage::getStoreConfig('payment/Bitcoins/walletbit_token') . '&item_name=' . $item_name . '&amount=' . $amount . '&returnurl=' . rawurlencode(Mage::getUrl('customer/account')) . '&additional=orderId=' . $orderId . '&currency=' . $order->getBaseCurrencyCode() . '&test=0';

			$payment->setIsTransactionPending(true); // status will be PAYMENT_REVIEW instead of PROCESSING

			$invoiceId = Mage::getModel('sales/order_invoice_api')->create($orderId, array());
			Mage::getSingleton('customer/session')->setRedirectUrl($url);

			return $this;
		}

		public function getOrderPlaceRedirectUrl()
		{
			if (Mage::getStoreConfig('payment/Bitcoins/onsite'))
				return '';
			else
				return Mage::getSingleton('customer/session')->getRedirectUrl();
		}
	}
?>