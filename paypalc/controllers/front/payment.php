<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class paypalcpaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->display_column_left = false;
		parent::initContent();

		$cart = $this->context->cart;
	
		if (!$this->module->checkCurrency($cart))
			Tools::redirect('index.php?controller=order');

			
			
			$address = new Address($cart->id_address_invoice);		
			$country = (string)Country::getIsoById($address->id_country);
		    $state = (string)State::getNameById($address->id_state);

			$cartproduct = new Cart($this->context->cookie->id_cart); 
			$products=$cartproduct->getProducts();
			
			$amount = $this->context->cart->getOrderTotal(true);

			$taxes = $amount - $this->context->cart->getOrderTotal(false);

			
		
		$this->context->smarty->assign(array(
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'isoCode' => $this->context->language->iso_code,
			'this_path' => $this->module->getPathUri(),
			'this_path_paypalc' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
			'PERSONNAME'=>$address->lastname.' '.$address->firstname,
			'SHIPTOSTREET'=>$address->address1,
			'SHIPTOCITY'=>$address->city,
			'SHIPTOSTATE'=>$state,
			'SHIPTOCOUNTRYCODE'=>$country,
			'SHIPTOZIP'=>$address->postcode,
			'PRODUCTS'=>$products,
			'token'=>Tools::passwdGen(36),
			'SHIPPINGAMT'=>(float)$this->context->cart->getTotalShippingCost()
		)); 

		$this->setTemplate('payment_execution.tpl');
	}
}
