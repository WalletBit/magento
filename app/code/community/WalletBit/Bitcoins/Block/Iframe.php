<?php
	class WalletBit_Bitcoins_Block_Iframe extends Mage_Checkout_Block_Onepage_Payment
	{
		protected function _construct()
		{      
			$this->setTemplate('bitcoins/iframe.phtml');
			parent::_construct();
		}
		
		// create an invoice and return the url so that iframe.phtml can display it
		public function GetIframeUrl()
		{
			if (!Mage::getStoreConfig('payment/Bitcoins/onsite'))
				return 'disabled';

			$quote = $this->getQuote();

			$quoteId = $quote->getId();
			if (Mage::getModel('Bitcoins/ipn')->GetQuotePaid($quote->getId()))
				return 'paid'; // quote's already paid, so don't show the iframe

			Mage::getSingleton('core/session', array('name'=>'frontend'));
			$session = Mage::getSingleton('checkout/session');

			$item_name = '';

			foreach ($session->getQuote()->getAllItems() as $item)
			{
				$item_name .= 'SKU:' . $item->getSku() . ', ';
				$item_name .= $item->getName() . ', ';
				$item_name .= 'Qty:' . $item->getQty() . ' - ';
			}

			$url = 'https://walletbit.com/pay?token=' . Mage::getStoreConfig('payment/Bitcoins/walletbit_token') . '&item_name=' . $item_name . '&amount=' . $quote->getGrandTotal() . '&returnurl=' . rawurlencode(Mage::getUrl('customer/account')) . '&additional=quoteId=' . $quoteId . '&currency=' . $quote->getQuoteCurrencyCode() . '&test=0';

			return $url . '&view=iframe';
		}
	}
?>