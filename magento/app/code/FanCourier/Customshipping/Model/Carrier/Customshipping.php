<?php
 namespace FanCourier\Customshipping\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Config;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Psr\Log\LoggerInterface;
 
class Customshipping extends AbstractCarrier implements CarrierInterface
{
 
	/**
	 
	* Carrier's code
	*
	* @var string
	*/
	 
	protected $_code = 'fancourier_customshipping';
	/**
	* Whether this carrier has fixed rates calculation
	*
	* @var bool
	*/
	 
	protected $_isFixed = true;
	/**
	* @var ResultFactory
	*/
	 
	protected $_rateResultFactory;
	/**
	* @var MethodFactory
	*/
	 
	protected $_rateMethodFactory;
	/**
	* @param ScopeConfigInterface $scopeConfig
	* @param ErrorFactory $rateErrorFactory
	* @param LoggerInterface $logger
	* @param ResultFactory $rateResultFactory
	* @param MethodFactory $rateMethodFactory
	* @param array $data
	*/
	 
	public function __construct(
	ScopeConfigInterface $scopeConfig,
	ErrorFactory $rateErrorFactory,
	LoggerInterface $logger,
	ResultFactory $rateResultFactory,
	MethodFactory $rateMethodFactory,
	array $data = []
	) {
	 
	$this->_rateResultFactory = $rateResultFactory;
	$this->_rateMethodFactory = $rateMethodFactory;
	parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
	}
	 
	/**
	* Generates list of allowed carrier`s shipping methods
	* Displays on cart price rules page
	*
	* @return array
	* @api
	*/
	 
	public function getAllowedMethods()
	{
	return [$this->getCarrierCode() => __($this->getConfigData('name'))];
	}
	/**
	* Collect and get rates for storefront
	*
	* @SuppressWarnings(PHPMD.UnusedFormalParameter)
	* @param RateRequest $request
	* @return DataObject|bool|null
	* @api
	*/
 
 
	
	public function collectRates(RateRequest $request)
	{
		if (!$this->getConfigFlag('active')) {
		return false;
		}  
		 
		$price=array();
		
		$username = $this->getConfigData('username');
		$parola = $this->getConfigData('password');
		$clientid = $this->getConfigData('clientid');
		$parcel = $this->getConfigData('parcel');
		$labels = $this->getConfigData('labels');
		$ramburs = $this->getConfigData('ramburs');
		$content = $this->getConfigData('content');
		$contcolector = $this->getConfigData('contcolector');
		$redcode = $this->getConfigData('redcode');
		$express = $this->getConfigData('express');
		$paypoint = $this->getConfigData('paypoint');
        $keba = $this->getConfigData('keba');
		$paymentdest = $this->getConfigData('paymentdest');
		$paymentrbdest = $this->getConfigData('paymentrbdest');
		$payment0 = $this->getConfigData('payment0');
		$min_gratuit_tara = $this->getConfigData('min_gratuit_tara');
		$min_gratuit_bucuresti = $this->getConfigData('min_gratuit_bucuresti');
		$suma_fixa = $this->getConfigData('suma_fixa');
		$observatii = $this->getConfigData('comment');
		$asigurare = $this->getConfigData('asigurare');
		$totalrb = $this->getConfigData('totalrb');
		$onlyadm = $this->getConfigData('onlyadm');
		$fara_tva = $this->getConfigData('fara_tva');
		$doar_km = $this->getConfigData('doar_km');
		
		$pers_contact_expeditor = $this->getConfigData('pers_contact');
		$deschidere_livrare = $this->getConfigData('deschidere');
		$epod = $this->getConfigData('ePOD');
		
		$msg="Comanda nu a fost procesata de catre FAN Courier. Corectati datele de livrare conform mesajului de mai jos: <br><br/>";
		$plata_expeditiei_ramburs = "";
		
		$currencyrate = 1;
		
		
		//optiuni
        $optiuni = '';
        if ($deschidere_livrare == 1){
            $optiuni .= 'A';
        }
		
		if ($epod == 1) $optiuni .= 'X';
		
		
		
		if (is_numeric($min_gratuit_tara)) $min_gratuit_tara = $min_gratuit_tara + 0; else $min_gratuit_tara = 0 + 0;
        if (is_numeric($min_gratuit_bucuresti)) $min_gratuit_bucuresti = $min_gratuit_bucuresti + 0; else $min_gratuit_bucuresti = 0 + 0;
        if (is_numeric($suma_fixa)) $suma_fixa = $suma_fixa + 0; else $suma_fixa = 0 + 0;
		
		if ($parcel){
            $plic="0";
            if (is_numeric($labels)){
                $colet=$labels;
            } else {
                $colet=1;
            }
        } else {
            $colet="0";
            if (is_numeric($labels)){
                $plic=$labels;
            } else {
                $plic=1;
            }
        }
		
		
		if ($totalrb){
            $totalrb = "1";
        } else {
            $totalrb = "0";
        }
		
		$q = '';
        foreach ($request->getAllItems() as $item) {
            $q = $item->GetId();
			
        }
		
		
		
		
		//testing
		
		//creare obiect sesiune
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->get('Magento\Customer\Model\Session');  
	

		if($customerSession->isLoggedIn()) {
           
			//  User logat 
			$this->_logger->debug(json_encode("Logged User =====================================<br>"));
			
			
		} else {

			//  User nelogat
			$this->_logger->debug(json_encode("Guest User =====================================<br>"));
			
			
		}

		
		//end testing
		
		
		
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sales_flat_quote_address = $resource->getTableName('quote_address');
        $sales_flat_quote_item = $resource->getTableName('quote_item');
        $directory_country_region = $resource->getTableName('directory_country_region');
        $sales_flat_quote_payment = $resource->getTableName('quote_payment');
		
		if (!$this->isActive()) {
            return false;
        }
		
		if (is_numeric($q)){
			
			$query = "SELECT $sales_flat_quote_address.*
			  FROM $sales_flat_quote_address
			  LEFT JOIN $sales_flat_quote_item ON $sales_flat_quote_item.quote_id = $sales_flat_quote_address.quote_id
			  WHERE $sales_flat_quote_address.address_type='shipping' AND $sales_flat_quote_item.item_id = {$q}";

            $readConnection = $resource->getConnection('core_read');
            $results = $readConnection->fetchAll($query);
			
			
			
			if (count($results)>0){
				
				
				if ($asigurare){
                    $valoaredeclarata = number_format(round((float)$results[0]["base_subtotal_with_discount"],2), 2, '.', '');
                } else {
                    $valoaredeclarata = 0;
                }
				
				$greutate = number_format(round((float)$results[0]["weight"],0), 0, '.', '');
               
				
                if ($greutate>1){
                    $plic=0;
                    if (is_numeric($labels)){
                        $colet=$labels;
                    } else {
                        $colet=1;
                    }
                }
				
				if (round((float)$results[0]["weight"],0)>5){ 
					$redcode = false;
				}

                $lungime  = 0;
                $latime   = 0;
                $inaltime = 0;
				
				if ($paymentdest){
                    $plata_expeditiei="destinatar";
                }else{
                    $plata_expeditiei="expeditor";
                }
				
			
/* 				if ($cashondelivery){
                    $query_payment = "SELECT distinct method FROM $sales_flat_quote_payment WHERE quote_id = {$results[0]["quote_id"]}";
					
                    $readConnection_payment = $resource->getConnection('core_read');
					$results_payment = $readConnection_payment->fetchAll($query_payment);
					foreach ($results_payment as $result_payment) {
                        $payment_cashondelivery = $result_payment["method"];
                    }
                } */
				
				$rambursare_number = 0 + 0;
                $rambursare = '';
				
											   
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				$cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
				$shippingAddress = $cart->getQuote()->getShippingAddress();
			    $detalii_dest = $shippingAddress->getData();
				//$this->_logger->debug('detalii dest'.json_encode($detalii_dest));
				$localitate_dest = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $detalii_dest["city"]);
				$judet_dest = iconv("UTF-8", "ISO-8859-1//TRANSLIT",  $detalii_dest["region"]);
                $telefon = $detalii_dest["telephone"];
                $strada = $detalii_dest["street"];
				
				
                $country_id = $detalii_dest["country_id"];
                $postalcode = str_pad($detalii_dest["postcode"],6,"0");
                $nume_destinatar = $detalii_dest["firstname"]." ".$detalii_dest["lastname"];
				$nume_destinatar=urlencode($nume_destinatar);
                				
				if (isset($detalii_dest["email"]) and trim($detalii_dest["email"]) != ""){
					$email = $detalii_dest["email"];
				} else {
					$email = "";
				}
				
				//iulianm comutare intre valoarea minima bucuresti si valoarea minima pe tara
				if (strtolower($localitate_dest) == 'bucuresti' and $min_gratuit_bucuresti!=0) {
                    $min_gratuit = $min_gratuit_bucuresti;
                } else {
                    $min_gratuit = $min_gratuit_tara;
                }
				
				
				//if ($ramburs and ($cashondelivery==0 or ($cashondelivery==1 and $payment_cashondelivery=='cashondelivery')))
				if ($ramburs){ 
                    if ($contcolector){
                        $rambursare = number_format(round((float)$results[0]["base_subtotal_with_discount"],2), 2, '.', '');
                        $rambursare_number = round((float)$results[0]["base_subtotal_with_discount"],2)+0;
					


                        if ($min_gratuit<=$rambursare_number and $min_gratuit!=0){
                            $totalrb="0";

                        }
                        //sfarsit

                        if ($paymentrbdest){
                            $plata_expeditiei_ramburs="destinatar";
                        }else{
                            $plata_expeditiei_ramburs="expeditor";
                        }
                    } else {
                        $rambursare = (string)number_format(round((float)$results[0]["base_subtotal_with_discount"],2), 2, '.', '')." LEI";
                        $rambursare_number = round((float)$results[0]["base_subtotal_with_discount"],2)+0;
						


                        if ($min_gratuit<=$rambursare_number and $min_gratuit!=0){
                            $totalrb="0";
                        }
                        //sfarsit

                        //plata transport ramburs
                        if ($paymentrbdest){
                            $plata_expeditiei_ramburs="destinatar";
                        } else {
                            $plata_expeditiei_ramburs="expeditor";
                        }
                    }
                } else {
                    $rambursare_number = 0;
                    $rambursare = '';

                }
				
				
				//Daca nu exista ramburs
                if ($rambursare ==''){
                    $totalrb = "0";
                    $rambursare = 0;
                    $contcolector= false;
                }
				
							
				
				$continut='';
                if ($content){
                    $query_sku = "SELECT * FROM $sales_flat_quote_item WHERE quote_id = {$results[0]["quote_id"]} order by sku";
                    $readConnection_sku = $resource->getConnection('core_read');
                    $results_sku = $readConnection_sku->fetchAll($query_sku);
					
					

                    foreach ($results_sku as $result_sku){
						if ($continut != ''){
							$continut = $continut.", ".$result_sku["sku"];
						} else {
							$continut = $result_sku["sku"];
						}
                    }
                }
                  
				   //$this->_logger->debug('continut'.json_encode($results_sku));
				
				if (isset($detalii_dest["company"]) and trim($detalii_dest["company"]) != ""){
					$persoana_contact = $detalii_dest["company"];
				} else {
					$persoana_contact = "";
				}
				
                //cand min gratuit mai mic ca ramburs plata la expeditor
                if ($min_gratuit<$rambursare_number and $min_gratuit!=0){
                    $plata_expeditiei="expeditor";
                }
				
				//initializare result
				$result = $this->_rateResultFactory->create();
				if($country_id == "RO"){
				
				$url = 'http://www.selfawb.ro/order.php';
                    $c = curl_init ($url);
                    curl_setopt ($c, CURLOPT_POST, true);
                    //curl_setopt ($c, CURLOPT_POSTFIELDS, "username=$username&user_pass=$parola&client_id=$clientid&return=services");
                    curl_setopt ($c, CURLOPT_POSTFIELDS, "username=$username&user_pass=$parola&client_id=$clientid&return=services&ridicare=$paypoint&keba=$keba&greutate=$greutate&ramburs=$rambursare&plata=$plata_expeditiei&telefon=$telefon");             
					curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
                    $page = curl_exec ($c);
                    curl_close ($c);
					
					$servicii_data = str_getcsv($page,"\n");
					
									
					foreach($servicii_data as $tip_serviciu_info){

						unset($tip_serviciu);
						unset($price_standard);
						unset($link_standard); 
						//unset($error_self);
					
						$tip_serviciu_info = str_replace('"','',$tip_serviciu_info);
						if ((!$contcolector or round($rambursare, 0)==0)) {
							
                            $tip_serviciu = explode(",",$tip_serviciu_info);
							if ($tip_serviciu[1]==0 and (($tip_serviciu[2]==0 and $tip_serviciu[3]==0) or ($tip_serviciu[2]==1 and $redcode) or ($tip_serviciu[3]==1 and $express))){
                                
								//setare optiuni  in functie de srviciul START
								
								if(strpos($tip_serviciu[0], 'Keba') !== false )
								{
									$optiuni = 'H';
								}else{
									if(strpos($tip_serviciu[0], 'Collect') !== false ){
										$optiuni = 'F';
									}
								}
								$url = 'http://www.selfawb.ro/order.php';
                                $c = curl_init ($url);
                                curl_setopt ($c, CURLOPT_POST, true);
                                curl_setopt ($c, CURLOPT_POSTFIELDS, "username=$username&user_pass=$parola&client_id=$clientid&plata_expeditiei=$plata_expeditiei&tip_serviciu=$tip_serviciu[0]&localitate_dest=$localitate_dest&judet_dest=$judet_dest&plic=$plic&colet=$colet&greutate=$greutate&lungime=$lungime&latime=$latime&inaltime=$inaltime&valoare_declarata=$valoaredeclarata&plata_ramburs=$plata_expeditiei_ramburs&ramburs=$rambursare&pers_contact_expeditor=$pers_contact_expeditor&observatii=$observatii&continut=$continut&nume_destinatar=$nume_destinatar&persoana_contact=$persoana_contact&telefon=$telefon&email=$email&strada=$strada&postalcode=$postalcode&totalrb=$totalrb&admin=$onlyadm&fara_tva=$fara_tva&suma_fixa=$suma_fixa&doar_km=$doar_km&optiuni=$optiuni&keba=$keba&ridicare=$paypoint");
                                curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
                                $page = curl_exec ($c);
                                curl_close ($c);
                                $result_self = explode("|||",$page);
								
								if(count($result_self) > 1){
									if (!($payment0) and ($min_gratuit>$rambursare_number or $min_gratuit==0)){
										$price_standard = $result_self[0];
									} else {
										$price_standard = 0;$suma_fixa=0;
									}
									
									//Standard fara ramburs
									if ($rambursare_number==0){
										$valoare_produse=round((float)$results[0]["grand_total"],2)+0;
										if ($min_gratuit<$valoare_produse){
											$price_standard = 0;
										}
									}
									
									$link_standard = $result_self[1];
									
									$price_standard = $price_standard / $currencyrate;
								} else {
									$error_self = $result_self[0];	
                                    
								
																
								}
								if (isset($price_standard) and is_numeric($price_standard) and isset($link_standard) and $link_standard!=""){
                                    $method = $this->_rateMethodFactory->create();
                                    $method->setCarrier($this->getCarrierCode());
									$method->setCarrierTitle($this->getConfigData('title'));
                                    $method->setMethod(str_replace(" ","_",$tip_serviciu[0]));
                                    //$method->setMethodTitle("<a href=\"http://www.selfawb.ro/order.php?order_id=$link_standard\" target=\"_blank\">$tip_serviciu[0]</a>");
                                   if(strpos($tip_serviciu[0], 'Keba') !== false ){
										$method->setMethodTitle( "<a id=\"ebox\" href=\"https://www.selfawb.ro/order.php?order_id=$link_standard&type=ebox\" target=\"_blank\">Ridicare din punct fix (eBOX)</a>");
									} else{
										if (strpos($tip_serviciu[0], 'Collect') !== false ){
											$method->setMethodTitle("<a id=\"collect_point\" href=\"http://www.selfawb.ro/order.php?order_id=$link_standard&type=collect\" target=\"_blank\">Ridicare din punct fix (Paypoint)</a>");
										}else{
											$method->setMethodTitle("<a href=\"http://www.selfawb.ro/order.php?order_id=$link_standard\" target=\"_blank\">$tip_serviciu[0]</a>");
										}
									}
									
									$method->setPrice($price_standard);
                                    $method->setCost($price_standard);
                                    $result->append($method);
                                }else{
                                    if ($tip_serviciu[2]==0 and $tip_serviciu[3]==0){
										$error = $this->_rateErrorFactory->create();
                                        $error->setCarrier($this->getCarrierCode());
										$error->setCarrierTitle($this->getConfigData('title'));
										$errorTitle = "abc";
										$error->setErrorMessage($errorTitle);
                                        $error->setErrorMessage($msg.$error_self);
                                        $result->append($error);	
										return $result;										
                                    }
                                }
								
							}
                            
                        }
                        else
                        {   
                            $tip_serviciu = explode(",",$tip_serviciu_info);
                            if ($tip_serviciu[1]==1 and (($tip_serviciu[2]==0 and $tip_serviciu[3]==0) or ($tip_serviciu[2]==1 and $redcode) or ($tip_serviciu[3]==1 and $express))){
                                
								if(strpos($tip_serviciu[0], 'Collect') !== false ){
										$optiuni = 'F';
									}
								
								$url = 'http://www.selfawb.ro/order.php';
                                $c = curl_init ($url);
                                curl_setopt ($c, CURLOPT_POST, true);
                                curl_setopt ($c, CURLOPT_POSTFIELDS, "username=$username&user_pass=$parola&client_id=$clientid&plata_expeditiei=$plata_expeditiei&tip_serviciu=$tip_serviciu[0]&localitate_dest=$localitate_dest&judet_dest=$judet_dest&plic=$plic&colet=$colet&greutate=$greutate&lungime=$lungime&latime=$latime&inaltime=$inaltime&valoare_declarata=$valoaredeclarata&plata_ramburs=$plata_expeditiei_ramburs&ramburs=$rambursare&pers_contact_expeditor=$pers_contact_expeditor&observatii=$observatii&continut=$continut&nume_destinatar=$nume_destinatar&persoana_contact=$persoana_contact&telefon=$telefon&email=$email&strada=$strada&postalcode=$postalcode&totalrb=$totalrb&admin=$onlyadm&fara_tva=$fara_tva&suma_fixa=$suma_fixa&doar_km=$doar_km&optiuni=$optiuni&keba=$keba&ridicare=$paypoint");
                                curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
                                $page = curl_exec ($c);
                                curl_close ($c);
                                $result_self = explode("|||",$page);
								
								if(count($result_self) > 1){
									if (!($payment0) and ($min_gratuit>$rambursare_number or $min_gratuit==0)) {
										$price_standard = $result_self[0];
										
									} else {
										$price_standard = 0;
										$suma_fixa=0;
									}
									$link_standard = $result_self[1];
									
									$price_standard = $price_standard / $currencyrate;
								} else {
									$error_self = $result_self[0];
									
								}
								
								
                                if (isset($price_standard) and is_numeric($price_standard) and isset($link_standard) and $link_standard!=""){
                                    $method = $this->_rateMethodFactory->create();
                                    $method->setCarrier($this->getCarrierCode());
									$method->setCarrierTitle($this->getConfigData('title'));
                                    $method->setMethod(str_replace(" ","_",$tip_serviciu[0]));
                                    
									if(strpos($tip_serviciu[0], 'Keba') !== false ){
										$method->setMethodTitle ("<a id=\"ebox\" href=\"https://www.selfawb.ro/order.php?order_id=$link_standard&type=ebox\" target=\"_blank\">Ridicare din punct fix (eBOX)</a>");
									} else {
										if(strpos($tip_serviciu[0], 'Collect') !== false ){
											$method->setMethodTitle("<a id=\"collect_point\" href=\"http://www.selfawb.ro/order.php?order_id=$link_standard&type=collect\" target=\"_blank\">Ridicare din punct fix (Paypoint)</a>");
										}else{
											$method->setMethodTitle("<a href=\"http://www.selfawb.ro/order.php?order_id=$link_standard\" target=\"_blank\">$tip_serviciu[0]</a>");
										}
									}

									$method->setPrice($price_standard);
                                    $method->setCost($price_standard);
                                    $result->append($method);
                                }else{
                                    if ($tip_serviciu[2]==0 and $tip_serviciu[3]==0){
										$error = $this->_rateErrorFactory->create();
                                        $error->setCarrier($this->getCarrierCode());
										$error->setCarrierTitle($this->getConfigData('title'));
										$errorTitle = "abc";
										$error->setErrorMessage($errorTitle);
                                        $error->setErrorMessage($msg.$error_self);
                                        $result->append($error);
										return $result;
                                    }
                                }
                            }
                        }
					
					}
					
					return $result;
				}
			}
		}
	}
}