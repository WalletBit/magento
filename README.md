Copyright (C) 2012 by Kris

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

About
	Bitcoin payment via walletbit.com for Magento.

Version 0.1
	Currency convert between all currencies automatically.
	
System Requirements:
	WalletBit.com account
	Magento
	PHP 5+
	Curl PHP Extension
  
Configuration Instructions:
	1. Upload files to your Magento installation.
	2. Go to your Magento administration. System -> Configuration then Payment Methods, in the left menu. You may need to clear the store cache before WalletBit/Bitcoins shows up.
	3. In WalletBit.com IPN https://walletbit.com/businesstools/IPN Enter this link http://YOUR_MAGENTO_URL/index.php/walletbit_callback/ in IPN URL
	4. Enter a strong Security Word in WalletBit IPN.
	5. In module settings "E-Mail" <- set your WalletBit.com email.
	6. In module settings "Token" <- copy from WalletBit.com https://walletbit.com/businesstools/IPN "Token"
	7. In module settings "Security Word" <- Enter the Security Word you created in step 4.

	(optional) On-site Payment. Set to "Yes" to embed payment in the checkout page. Set to "No" for redirecting to walletbit.com to complete payment.