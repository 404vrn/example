<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Iblock,
	Bitrix\Main\Loader,
	Bitrix\Sale,
	Bitrix\Main\Application,
	Bitrix\Main\Entity,
	Bitrix\Highloadblock as HL,
	Bitrix\Main,
	Bitrix\Main\Localization\Loc as Loc,
	Bitrix\Main\Config\Option,
	Bitrix\Sale\Delivery,
	Bitrix\Sale\PaySystem,
	Bitrix\Sale\Order,
	Bitrix\Sale\DiscountCouponsManager,
	Bitrix\Main\Context;

Loader::includeModule("highloadblock");
Loader::includeModule('iblock');
Loader::includeModule('catalog');
Loader::includeModule('sale');

$get =$_GET;

$step = $get['step'];

$allStepData = (array)$allStepData;

switch ($step) {

	case '1':
	//Фабрики

		$hlbl = 37;

		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);

		$entity_data_class = $entity->getDataClass();

		$rsData = $entity_data_class::getList(array(

		   "select" => array("*"),

		   "order" => array("ID" => "ASC"),

		   "filter" => array()

		));

		while($arData = $rsData->Fetch()){
			
		   $stepData[$step][] = $arData['UF_NAIMENOVANIE'];

		   $allStepData[] = $arData;

		}

		$title = "Фабрика";

	break;

	case '2':
	//Вид изделия
		
		$hlbl = 38;

		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);

		$entity_data_class = $entity->getDataClass();

		$rsData = $entity_data_class::getList(array(
		   "select" => array("*"),

		   "order" => array("ID" => "ASC"),

		   "filter" => array("UF_FABRIKA"=>$get['val'], "UF_AKTIVNO"=>1,),

		));

		while($arData = $rsData->Fetch()){
			
			$stepData[$step][] = $arData['UF_NAIMENOVANIE'];

			$allStepData[] = $arData;
		}

		if ($_SESSION['CALC']['VID_IZDELIYA'] || $_SESSION['CALC']['FABRIKA']){

			unset($_SESSION['CALC']['VID_IZDELIYA']);

			unset($_SESSION['CALC']['FABRIKA']);
		}

		$_SESSION['CALC']['FABRIKA'] = $get['val'];

		$pageFields[$step]["Фабрика"] = $_SESSION['CALC']['FABRIKA'];

		$title = "Вид изделия";

	break;

	case '3':
	//Декоры

		$hlbl = 24;

		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);

		$entity_data_class = $entity->getDataClass();

		$rsData = $entity_data_class::getList(array(

		   "select" => array("*"),

		   "order" => array("ID" => "ASC"),

		   "filter" => array("UF_FABRIKA"=>$_SESSION['CALC']['FABRIKA'], "UF_ACTIVE" => 1,)

		));

		while($arData = $rsData->Fetch()){
			
			$stepData[$step][] = $arData['UF_NAZVANIE'];

			$allStepData[] = $arData;

		}

		if ($_SESSION['CALC']['VID_IZDELIYA']){

			unset($_SESSION['CALC']['VID_IZDELIYA']);

		}
			$_SESSION['CALC']['VID_IZDELIYA'] = $get['val'];

			$pageFields[$step]["Вид изделия"] = $_SESSION['CALC']['VID_IZDELIYA'];

			$title = "Декор";

		break;

	case '4':
	//Описание фрезеровки

		$hlbl = 29;

		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch(); 

		$entity = HL\HighloadBlockTable::compileEntity($hlblock); 
		
		$entity_data_class = $entity->getDataClass(); 

		$rsData = $entity_data_class::getList(array(
		   "select" => array("*"),
		   "order" => array("ID" => "ASC"),
		   "filter" => array("UF_VID_IZDELIYA"=>$_SESSION['CALC']['VID_IZDELIYA']) 
		));

		while($arData = $rsData->Fetch()){
			
			$stepData[$step][] = $arData['UF_NAZVANIE'];

			$allStepData[] = $arData;
		}
		
		if ($_SESSION['CALC']['DECOR']){

			unset($_SESSION['CALC']['DECOR']);

			unset($_SESSION['CALC']['DECOR_CAT']);

		}

		$title = "Описание фрезеровки";

		$pageFields[$step]["Декор"] = $_SESSION['CALC']['DECOR'];

		$_SESSION['CALC']['DECOR']  = $get['val'];

		$_SESSION['CALC']['DECOR_CAT']  = $get['cat'];

	break;

	case '5':
	//Допустимые фрезы по фасаду

		$hlbl = 32;

		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);

		$entity_data_class = $entity->getDataClass();

		$rsData = $entity_data_class::getList(array(

		   "select" => array("*"),

		   "order" => array("ID" => "ASC"),

		   "filter" => array("UF_FREZEROVKA"=>$get['val'])

		));

		while($arData = $rsData->Fetch()){
			
			$stepData[$step][$arData['UF_FREZA_PO_FASADU']]['name'] = $arData['UF_FREZA_PO_FASADU'];

			$stepData[$step][$arData['UF_FREZA_PO_FASADU']]['img'] = $arData['UF_IMAGE'];

			$allStepData[] = $arData;
		}

		if ($_SESSION['CALC']['FREZEROVKA']){

			unset($_SESSION['CALC']['FREZEROVKA']);

			unset($_SESSION['CALC']['PRISADKA']);

		}

		$_SESSION['CALC']['FREZEROVKA'] = $get['val'];

		$_SESSION['CALC']['PRISADKA'] = $get['prisadka'];

		$pageFields[$step]["Вид изделия"] =$_SESSION['CALC']['FREZEROVKA'];

		$pageFields[$step]['percent'] = '50';

		$title = "Фреза по фасаду";

	break;

	case '6':
	//Допустимые фрезы по краю

		$hlbl = 31;

		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch(); 

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);

		$entity_data_class = $entity->getDataClass();

		$rsData = $entity_data_class::getList(array(

		   "select" => array("*"),
		   "order" => array("ID" => "ASC"),
		   "filter" => array("UF_FREZEROVKA"=>$_SESSION['CALC']['FREZEROVKA'])

		));

		while($arData = $rsData->Fetch()){
			
			$stepData[$step][$arData['UF_FREZA_PO_KRAYU']]['name'] = $arData['UF_FREZA_PO_KRAYU'];
			$stepData[$step][$arData['UF_FREZA_PO_KRAYU']]['img'] = $arData['UF_IMAGE'];
			$allStepData[] = $arData;
		}

		if ($_SESSION['CALC']['FREZA_PO_FASADU']){

			unset($_SESSION['CALC']['FREZA_PO_FASADU']);

		}

		$stepData[$step] = array_unique($stepData[$step]);

		$_SESSION['CALC']['FREZA_PO_FASADU'] = $get['val'];

		$pageFields[$step]["Фреза по фасаду"] = $_SESSION['CALC']['FREZA_PO_FASADU'];

		$title = "Фреза по краю";

	break;

	case '7':
	//Допустимые плиты

	$hlbl = 33;

	$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

	$entity = HL\HighloadBlockTable::compileEntity($hlblock);

	$entity_data_class = $entity->getDataClass();

	$rsData = $entity_data_class::getList(array(

	   "select" => array("*"),
	   "order" => array("ID" => "ASC"),
	   "filter" => array("UF_FREZEROVKA"=>$_SESSION['CALC']['FREZEROVKA'])

	));

	while($arData = $rsData->Fetch()){
		
		$filter[] = $arData['UF_TOLSHINA_PLITI'];

	}

	//Плиты
		$hlbl = 23;

		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch(); 

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);

		$entity_data_class = $entity->getDataClass(); 

		$rsData = $entity_data_class::getList(array(

		   "select" => array("*"),
		   "order" => array("ID" => "ASC"),
		   "filter" => array("UF_TOLSHINA" => $filter)

		));

		while($arData = $rsData->Fetch()){
			
			$stepData[$step][] = $arData['UF_NAIMENOVANIE'];
			$allStepData[] = $arData;
		}
		
		if ($_SESSION['CALC']['FREZA_PO_KRAYU']){

			unset($_SESSION['CALC']['FREZA_PO_KRAYU']);
		}

		$_SESSION['CALC']['FREZA_PO_KRAYU'] = $get['val'];

		$pageFields[$step]["Фреза по краю"]= $_SESSION['CALC']['FREZA_PO_KRAYU'];

		$title = "Плита";

	break;

	case '8':
	//Допустимые типы фасадов
	$hlbl = 34;

	$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch(); 

	$entity = HL\HighloadBlockTable::compileEntity($hlblock);

	$entity_data_class = $entity->getDataClass(); 

	$rsData = $entity_data_class::getList(array(

	   "select" => array("*"),
	   "order" => array("ID" => "ASC"),
	   "filter" => array("UF_FREZEROVKA"=>$_SESSION['CALC']['FREZEROVKA'], "UF_AKTIVNO"=>1,)

	));

	while($arData = $rsData->Fetch()){
		
		$stepData[$step][] = $arData['UF_TIPI_FASADOV'];
		$allStepData[] = $arData;
		$_SESSION['CALC']['FREZ_IMG'][$arData['UF_TIPI_FASADOV']][] = $arData['UF_IMAGE'] ;

	}

	if ($_SESSION['CALC']['PLITA']){

		unset($_SESSION['CALC']['PLITA']);
	}

	$title = "Допустимые типы фасадов";

	$_SESSION['CALC']['PLITA'] = $get['val'];

	$pageFields[$step]["Плита"] = $_SESSION['CALC']['PLITA'];
	
	
	//Базовая стоимость
	$hlbl = 39;

	$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

	$entity = HL\HighloadBlockTable::compileEntity($hlblock);

	$entity_data_class = $entity->getDataClass(); 

	$plita = preg_replace('/\D+/', '', $_SESSION['CALC']['PLITA']);

	$rsData = $entity_data_class::getList(array(

	   "select" => array("*"),

	   "order" => array("ID" => "ASC"),

	   "filter" => array("UF_KATEGORIYA"=>$_SESSION['CALC']['DECOR_CAT'], "%UF_TOLSHINA" =>$plita)

	));

	if ($_SESSION['CALC']['C_BASE_PRICE']){

		unset($_SESSION['CALC']['C_BASE_PRICE']);

	}		
	while($arData = $rsData->Fetch()){

		$_SESSION['CALC']['C_BASE_PRICE'] = $arData['UF_STOIMOST'];
	}

	break;

	case '9':
	//Ограничения по размерам
	$hlbl = 35;

	$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch(); 

	$entity = HL\HighloadBlockTable::compileEntity($hlblock);

	$entity_data_class = $entity->getDataClass(); 
	
	$rsData = $entity_data_class::getList(array(

	   "select" => array("*"),

	   "order" => array("ID" => "ASC"),

	   "filter" => array("UF_FREZEROVKA"=>$_SESSION['CALC']['FREZEROVKA'], "UF_TIP_FASADA" =>$get['val'])

	));

	while($arData = $rsData->Fetch()){

		$stepData[$step]['minH'] = $arData['UF_MIN_H'];

		$stepData[$step]['maxH'] = $arData['UF_MAX_H'];

		$stepData[$step]['minW'] = $arData['UF_MIN_W'];

		$stepData[$step]['maxW'] = $arData['UF_MAX_W'];

		$stepData[$step]['TIP_FASADA'] = $get['val'];

		$allStepData[] = $arData;

	}

	if ($_SESSION['CALC']['TIP_FASADA']){

		unset($_SESSION['CALC']['TIP_FASADA']);

	}

	$_SESSION['CALC']['TIP_FASADA'] = $get['val'];

	$pageFields[$step]['percent'] = '100';

	  if ($allStepData[0] == false) {

		$rsData = $entity_data_class::getList(array(

		   "select" => array("*"),

		   "order" => array("ID" => "ASC"),

		   "filter" => array("UF_FREZEROVKA"=>$_SESSION['CALC']['FREZEROVKA'], "UF_TIP_FASADA" =>"")

		));

		while($arData = $rsData->Fetch()){

			$stepData[$step]['minH'] = $arData['UF_MIN_H'];

			$stepData[$step]['maxH'] = $arData['UF_MAX_H'];

			$stepData[$step]['minW'] = $arData['UF_MIN_W'];

			$stepData[$step]['maxW'] = $arData['UF_MAX_W'];

			$stepData[$step]['TIP_FASADA'] = $get['val'];

			$allStepData[] = $arData;
		}

	  }

	break;

	case '10':
	//Рассчет изделия

		$square = (int) ($get['square'] * 1000) / 1000;

		$count = $get['count'];

		$price = $_SESSION['CALC']['C_BASE_PRICE'];

	break;

	case '11':

	//Рассчет всех изделий
	
	if ($get['ITEMS'][0]){

		$resultCost = 0;

		foreach ($get['ITEMS'] as $item) {

			$width = (int)$item['WIDTH'];

			$height = (int)$item['HEIGHT'];

			$square = ($width * $height * 0.001)/1000;

			$cost = ( round($square, 3) * $item['C_BASE_PRICE']) * $item['COUNT'];

			$item['COST'] = $cost;

			$resultCost +=  $cost;

			$resultCost = round($resultCost,2);

		$_SESSION['CALC']['ITEMS'] = $get['ITEMS'];

		}

	}

	break;

	case '12':
	//Добавление элементов в инфоблок

	if ($_SESSION['CALC']['ITEMS'][0]){
			
		CModule::IncludeModule('iblock');  

		//ЗАКАЗ
		function getPropertyByCode($propertyCollection, $code)  {

			foreach ($propertyCollection as $property)
			{
				if($property->getField('CODE') == $code)

					return $property;

			}

		}

		$siteId = \Bitrix\Main\Context::getCurrent()->getSite();

		$fio = 'Иванов Иван Иванович';

		$phone = '79002003040';

		$email = '1111@kdmc.ru';

		$currencyCode = Option::get('sale', 'default_currency', 'RUB');

		DiscountCouponsManager::init();

		//$order = Order::create($siteId, \CSaleUser::GetAnonymousUserID());
		$order = Order::create($siteId, Sale\Fuser::getId());

		$order->setPersonTypeId(1);

		$basket = Sale\Basket::create($siteId);
		//ЗАКАЗ
		
		foreach ($_SESSION['CALC']['ITEMS'] as $item) {
			
			$number = rand(9999, 99999999999);

			$PROP = Array();

			$newItem = new CIBlockElement;

			$square = ((int)$item['WIDTH'] * (int)$item['HEIGHT'] * 0.001)/1000;

			$price = round($square, 3) * $item['C_BASE_PRICE'];

			$customPrice = round($price, 2);

			$quantity = (int)$item['COUNT'];

			$TotalPrice = $customPrice * $quantity;

			$PROP['PRICE'] = $customPrice;

			$itemNameField = $item['FREZEROVKA'].' '.$item['NAME'].' '.$item['DECOR'].' '.$item['WIDTH'].' x '.$item['HEIGHT'].' | '.$number;

			$itemFabrika = $item['FABRIKA'];

			$itemDecor = $item['DECOR'];

			$itemDecorCat = $item['DECOR_CAT'];

			$itemFrezerovka = $item['FREZEROVKA'];

			$itemVidIzdeliya = $item['VID_IZDELIYA'];

			$itemFrezaPoFasadu = $item['FREZA_PO_FASADU'];

			$itemFrezaPoKrayu = $item['FREZA_PO_KRAYU'];

			$itemPlita = $item['PLITA'];

			$itemCBasePrice = $item['C_BASE_PRICE'];

			$itemHeight = $item['HEIGHT'];

			$itemWidth = $item['WIDTH'];

			$itemCount = $item['COUNT'];

			$itemPrice = $customPrice;
			
			$tmpAr = Array(

				"NAME" => $itemNameField,

				"PREVIEW_TEXT" => $itemNameField,

				"DETAIL_TEXT" => $itemNameField

			);

			foreach ($item as $key => $value) {

				$PROP[$key] = $value;

			}

			$arLoadProductArray = Array(

				"ACTIVE_FROM" => date('d.m.Y H:i:s'),

				"MODIFIED_BY" => $USER->GetID(),

				"IBLOCK_SECTION_ID" => false,

				"IBLOCK_ID" => 11,

				"ACTIVE" => "Y",

				"PROPERTY_VALUES"=> $PROP, 

				"DETAIL_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$item['IMAGE']),

				"PREVIEW_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$item['IMAGE'])

			);

			$arLoadProductArray = array_merge($tmpAr, $arLoadProductArray);
			
			if($newElement = $newItem->Add($arLoadProductArray)){

				CPrice::SetBasePrice($newElement,$customPrice, Bitrix\Currency\CurrencyManager::getBaseCurrency(), false, false, false);

					$itemBasePrice = Array(

						"PRODUCT_ID" => $newElement,

						"CATALOG_GROUP_ID" => 1,

						"PRICE" => $customPrice,

						"CURRENCY" => Bitrix\Currency\CurrencyManager::getBaseCurrency(),

					);
				
				$arLoadProductArray['PROPERTY_VALUES']['ID'] = $newElement;

				$newProducts[] = $arLoadProductArray['PROPERTY_VALUES'];

				$orderPrice += $arLoadProductArray['PROPERTY_VALUES']['PRICE'];

				$setPrice = new CPrice();

				$setPrice->Add($itemBasePrice,true);

				//$basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
				
			}

		}
		//ЗАКАЗ

			$basketName = "Расчет заказных позиций "."Фабрика ".$_SESSION['CALC']['FABRIKA']." ".$_SESSION['CALC']['VID_IZDELIYA'];

			$item = $basket->createItem('catalog', $newElement);

			$item->setFields(array(

				'QUANTITY' => 1,

				'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),

				'LID' => Bitrix\Main\Context::getCurrent()->getSite(),

				'PRICE' =>$orderPrice,

				'CUSTOM_PRICE' => 'Y',

				'NAME' => $basketName,
			));

		$order->setBasket($basket);

		$shipmentCollection = $order->getShipmentCollection();

		$shipment = $shipmentCollection->createItem();

		$service = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());

		$shipment->setFields(array(

			'DELIVERY_ID' => $service['ID'],

			'DELIVERY_NAME' => $service['NAME'],

		));

		$shipmentItemCollection = $shipment->getShipmentItemCollection();

		foreach ($order->getBasket() as $item){

			$shipmentItem = $shipmentItemCollection->createItem($item);

			$shipmentItem->setQuantity($item->getQuantity());
		}

		$paymentCollection = $order->getPaymentCollection();

		$payment = $paymentCollection->createItem();

		$paySystemService = PaySystem\Manager::getObjectById(1);

		$payment->setFields(array(

			'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),

			'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
		));

		$order->doFinalAction(true);

		$propertyCollection = $order->getPropertyCollection();

		$emailProperty = getPropertyByCode($propertyCollection, 'EMAIL');

		$emailProperty->setValue($email);

		$phoneProperty = getPropertyByCode($propertyCollection, 'PHONE');

		$phoneProperty->setValue($phone);

		$fioProperty = getPropertyByCode($propertyCollection, 'FIO');

		$fioProperty->setValue($fio);

		$order->setField('CURRENCY', $currencyCode);

		$order->setField('COMMENTS',json_encode($newProducts));

		$order->save();

		$orderId = $order->GetId();

		if ($orderId) {

			echo "Ваш заказ ".$orderId." успешно создан";
		}
		//ЗАКАЗ

	}


	break;

	default:
	//Фабрики

		$hlbl = 37;

		$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch(); 

		$entity = HL\HighloadBlockTable::compileEntity($hlblock);

		$entity_data_class = $entity->getDataClass(); 

		$rsData = $entity_data_class::getList(array(

		   "select" => array("*"),

		   "order" => array("ID" => "ASC"),

		   "filter" => array()

		));

		while($arData = $rsData->Fetch()){
			
		   $stepData[$step][] = $arData['UF_NAIMENOVANIE'];

		   $allStepData[] = $arData;
		   
		}

		$title = "Фабрика";
		
		break;
	}

if ($step == 1) {
//Фабрика
	?>
	<div id="step<?=$step?>" class="step-wrap accordion-item show">	
		<h5 class="accordion-header">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentStep<?=$step?>" aria-expanded="false" aria-controls="#contentStep<?=$step?>">
				<strong><i class="bi bi-<?=$step?>-square mx-2"></i><?=$title?></strong>
				<span id="headerStep<?=$step?>" class="mx-3"></span>
			</button>
		</h5>
		<div id="contentStep<?=$step?>" class="accordion-collapse collapse" data-bs-parent="#calc-res">	
			<div class="accordion-body"  style="display:flex; flex-wrap: wrap;" >
				<ul class="list-unstyled">
			<? foreach ($allStepData as $data) { ?>	
					<li><a href="javascript:void(0)" data-id=<?=$step?> data-val="<?=$data['UF_NAIMENOVANIE']?>" class="calculator-btn my-1" ><?=$data['UF_NAIMENOVANIE']?></a></li>
			<?}?>
				</ul>
			</div>
		</div>
	</div>
	<?
}
elseif ($step ==2 ) {
		//Вид изделия
			?>
	<div id="step<?=$step?>" class="step-wrap accordion-item">	
		<h5 class="accordion-header">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentStep<?=$step?>" aria-expanded="false" aria-controls="#contentStep<?=$step?>">
				<strong><i class="bi bi-<?=$step?>-square mx-2"></i><?=$title?></strong>
				<span id="headerStep<?=$step?>" class="mx-3"></span>
			</button>
		</h5>
		<div id="contentStep<?=$step?>" class="accordion-collapse collapse" data-bs-parent="#calc-res">	
			<div class="accordion-body"  style="display:flex; flex-wrap: wrap;" >
				<ul class="list-unstyled">
			<? foreach ($allStepData as $data) { ?>	
				<li><a href="javascript:void(0)" data-id=<?=$step?> data-val="<?=$data['UF_NAIMENOVANIE']?>" class="calculator-btn my-1" ><?=$data['UF_NAIMENOVANIE']?></a></li>
			<?}?>
				</ul>
			</div>
		</div>
	</div>
			<?
}

elseif ($step == 3) {
		//Декор
		?>
	<div id="step<?=$step?>" class="step-wrap accordion-item">	
		<h5 class="accordion-header">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentStep<?=$step?>" aria-expanded="false" aria-controls="#contentStep<?=$step?>">
				<strong><i class="bi bi-<?=$step?>-square mx-2"></i><?=$title?></strong>
				<span id="headerStep<?=$step?>" class="mx-3"></span>
			</button>
		</h5>
		<div id="contentStep<?=$step?>" class="accordion-collapse collapse" data-bs-parent="#calc-res">	
			<div class="accordion-body"  style="display:flex; flex-wrap: wrap;" >
			<? foreach ($allStepData as $data) {
			$img = CFile::GetPath($data['UF_IMAGE']);?>
				<div class="card col-3">
				  <img  width="200" src="<?=$img?> " class="card-img-top" alt=" ">
				  <div class="card-body">
				    <h5 class="card-title"><?=$data['UF_NAZVANIE']?></h5>
				    <ul class="list-group list-group-flush">
				    	<li class="list-group-item">Номер декора: <?=$data['UF_NOMER_DECORA']?></li>
				    	<li class="list-group-item">Категория декора: <?=$data['UF_KATEGORIYA']?></li>
				    	<li class="list-group-item">Направление текстуры: <?=$data['UF_NAPRAVLENIE_TEKSTURI']?></li>
				    	<li class="list-group-item">Тип декора: <?=$data['UF_TIP_DEKORA']?></li>
				    	<li class="list-group-item">Цвет декора: <?=$data['UF_TSVET_DECORA']?></li>
				    </ul>
				    <a href="javascript:void(0)" data-id="<?=$step?>" data-val="<?=$data['UF_NAZVANIE']?>" data-cat="<?=$data['UF_KATEGORIYA']?>" class="calculator-btn btn btn-primary mx-4">Выбрать</a>
				  </div>
				</div>
			<?}?>
			</div>
		</div>
	</div>	
		<?
}

elseif ($step == 4) {
		//Описание фрезеровки
		?>
	<div id="step<?=$step?>" class="step-wrap accordion-item">	
		<h5 class="accordion-header">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentStep<?=$step?>" aria-expanded="false" aria-controls="#contentStep<?=$step?>">
				<strong><i class="bi bi-<?=$step?>-square mx-2"></i><?=$title?></strong>
				<span id="headerStep<?=$step?>" class="mx-3"></span>
			</button>
		</h5>
		<div id="contentStep<?=$step?>" class="accordion-collapse collapse" data-bs-parent="#calc-res">	
			<div class="accordion-body"  style="display:flex; flex-wrap: wrap;" >
			<? foreach ($allStepData as $data) { 
			$img = CFile::GetPath($data['UF_IMAGE']);?>	
				<div class="card col-3" >
				  <img width="200" src="<?=$img?> " class="card-img-top" alt=" ">
				  <div class="card-body">
				    <h5 class="card-title"><?=$data['UF_NAZVANIE']?></h5>
				    <ul class="list-group list-group-flush">
				    	<li class="list-group-item">Тип фрезеровки: <?=$data['UF_TIP_FREZEROVKI']?></li>
				    	<li class="list-group-item">Добавление присадок: <?=$data['UF_RAZRESHENO_DOBAVLENIE_PRISADOK']?></li>
				    </ul>
				    <a href="javascript:void(0)" data-id="<?=$step?>" data-val="<?=$data['UF_NAZVANIE']?>" data-prisadka="<?=$data['UF_RAZRESHENO_DOBAVLENIE_PRISADOK']?>" class="calculator-btn btn btn-primary my-4">Выбрать</a>
				  </div>
				</div>
			<?}?>
		</div>
	</div>
	</div>	
		<?
}

elseif ($step == 5) {
		//фрезеровка фасада
?>
	<div id="step<?=$step?>" class="step-wrap accordion-item show">	
		<h5 class="accordion-header">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentStep<?=$step?>" aria-expanded="false" aria-controls="#contentStep<?=$step?>">
				<strong><i class="bi bi-<?=$step?>-square mx-2"></i><?=$title?></strong>
				<span id="headerStep<?=$step?>" class="mx-3"></span>
			</button>
		</h5>
		<div id="contentStep<?=$step?>" class="accordion-collapse collapse" data-bs-parent="#calc-res">	
			<div class="accordion-body"  style="display:flex; flex-wrap: wrap;" >
				<ul class="list-unstyled">
			<?foreach ($stepData[$step] as $data) {	

				$img = CFile::GetPath($data['img']);

			?>	
				<li><a href="javascript:void(0)" data-id=<?=$step?> data-val="<?=$data['name']?>" class="calculator-btn my-1" ><?=$data['name']?></a>
					<img src="<?=$img?>"  width="100"/></li>
			<?}?>
				</ul>
			</div>
		</div>
	</div>	
		<?
}

elseif ($step == 6) {
		//фрезеровка края

		?>
	<div id="step<?=$step?>" class="step-wrap accordion-item show">	
		<h5 class="accordion-header">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentStep<?=$step?>" aria-expanded="false" aria-controls="#contentStep<?=$step?>">
				<strong><i class="bi bi-<?=$step?>-square mx-2"></i><?=$title?></strong>
				<span id="headerStep<?=$step?>" class="mx-3"></span>
			</button>
		</h5>
		<div id="contentStep<?=$step?>" class="accordion-collapse collapse" data-bs-parent="#calc-res">	
			<div class="accordion-body"  style="display:flex; flex-wrap: wrap;" >
				<ul class="list-unstyled">
			<?foreach ($stepData[$step] as $data) {		
				$img = CFile::GetPath($data['img']);				
			?>	

				<li>
					<a href="javascript:void(0)" data-id="<?=$step?>" data-val="<?=$data['name']?>" class="calculator-btn my-1" ><?=$data['name']?></a>
					<img src="<?=$img?>"  width="100"/>
				</li>
			<?}?>
				<ul class="list-unstyled">
			</div>
		</div>
	</div>	
		<?
}

elseif ($step == 7) {
		//Плита
		?>
		
	<div id="step<?=$step?>" class="step-wrap accordion-item ">	
		<h5 class="accordion-header">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentStep<?=$step?>" aria-expanded="false" aria-controls="#contentStep<?=$step?>">
				<strong><i class="bi bi-<?=$step?>-square mx-2"></i><?=$title?></strong>
				<span id="headerStep<?=$step?>" class="mx-3"></span>
			</button>
		</h5>
		<div id="contentStep<?=$step?>" class="accordion-collapse collapse" data-bs-parent="#calc-res">	
			<div class="accordion-body"  style="display:flex; flex-wrap: wrap;" >
				<ul class="list-unstyled">
			<? foreach ($stepData[$step] as $data) { ?>	

					<li><a href="javascript:void(0)" data-id=<?=$step?> data-val="<?=$data?>" class="calculator-btn my-1" ><?=$data?></a></li>
			<?}?>
				</ul>
			</div>
		</div>
	</div>	
		<?
	}

elseif ($step == 8) {
	//Добавить изделие
	?>
	<div id="step<?=$step?>" class="step-wrap accordion-item">	
		<h5 class="accordion-header">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentStep<?=$step?>" aria-expanded="false" aria-controls="#contentStep<?=$step?>">
				<strong><i class="bi bi-<?=$step?>-square mx-2"></i><?=$title?></strong>
				<span id="headerStep<?=$step?>" class="mx-3"></span>
			</button>
		</h5>

		<div id="contentStep<?=$step?>" class="accordion-collapse collapse" data-bs-parent="#calc-res">	
			<div class="accordion-body"  style="display:flex; flex-wrap: wrap;" >
				<div class="dropdown" style="width:180px">
					<a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
					Добавить изделие
					</a>
					<ul class="dropdown-menu">
					<? foreach ($stepData[$step] as $data) { ?>	
						<li><a class="dropdown-item calculator-btn"  data-id="<?=$step?>" data-val="<?=$data?>" href="javascript:void(0)"><?=$data?></a></li>
					<? }?>
					</ul>
				</div>
			</div>
		</div>
	</div>
		<form id="calc-form" class="calc-form requires-validation" > 
			<div id="step9" class="step-wrap" style="display:flex; flex-wrap: wrap;">	
			</div>
			<a class="send-form btn btn-primary my-3" data-id="10" href="javascript:void(0)"><i class="bi bi-calculator mx-2"></i>Рассчитать стоимость</a>
		</form>

<?
}

elseif ($step == 9) {
	//Список изделий

		if (isset($_SESSION["CALC"]["IZDELIE"])){
		
			$_SESSION["CALC"]["IZDELIE"] ++;
		}
		else{

			$_SESSION["CALC"]["IZDELIE"] = 0;
		}
		?>
		<div id="item<?=$_SESSION["CALC"]["IZDELIE"]?>" class="col-3 my-3 px-3 card">
			<h5><?=$title?></h5>
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].ID" type="hidden" value="<?=$_SESSION["CALC"]["IZDELIE"];?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].NAME" type="hidden" value="<?=$stepData[$step]['TIP_FASADA']?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].FABRIKA" type="hidden" value="<?=$_SESSION["CALC"]["FABRIKA"];?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].VID_IZDELIYA" type="hidden" value="<?=$_SESSION["CALC"]["VID_IZDELIYA"];?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].DECOR" type="hidden" value="<?=$_SESSION["CALC"]["DECOR"];?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].DECOR_CAT" type="hidden" value="<?=$_SESSION["CALC"]["DECOR_CAT"];?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].FREZEROVKA" type="hidden" value="<?=$_SESSION["CALC"]["FREZEROVKA"];?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].PRISADKA" type="hidden" value="<?=$_SESSION["CALC"]["PRISADKA"];?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].FREZA_PO_FASADU" type="hidden" value="<?=$_SESSION["CALC"]["FREZA_PO_FASADU"];?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].FREZA_PO_KRAYU" type="hidden" value="<?=$_SESSION["CALC"]["FREZA_PO_KRAYU"];?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].PLITA" type="hidden" value="<?=$_SESSION["CALC"]["PLITA"];?>" />
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].C_BASE_PRICE" type="hidden" value="<?=$_SESSION["CALC"]["C_BASE_PRICE"];?>" />
			<? foreach ($_SESSION['CALC']['FREZ_IMG'] as $key => $value) {
				if ($key == $stepData[$step]['TIP_FASADA']) {

					$img = CFile::GetPath($value[0]);
				?>
			<input name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].IMAGE" type="hidden" value="<?=$img?>" />
				<?
				}
			} ?>			
				<img class="img-thumbnail" src="<?=$img?>"/>
					<div data-step="<?=$step?>" >
						<p><strong><?=$stepData[$step]['TIP_FASADA']?></strong>
						<div class="calculate-data">
							<div class="input-group my-1">
								<label >Высота мм</label>
								<input class="form-control calc-params height" placeholder="от: <?=$stepData[$step]["minH"]?> до: <?=$stepData[$step]["maxH"]?>" name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].HEIGHT" type="number" min='<?=$stepData[$step]["minH"]?>' max='<?=$stepData[$step]["maxH"]?>'  required />
								<div class="invalid-feedback invalH text-end">
								Пожалуйста, введите высоту изделия от <?=$stepData[$step]["minH"]?> до <?=$stepData[$step]["maxH"]?>
								</div>
								<div class="valid-feedback text-end">
								Данные введены верно.
								</div>
							</div>
							<div class="input-group my-1">
								<label>Ширина мм</label>
								<input class="form-control calc-params  " placeholder="от: <?=$stepData[$step]["minW"]?> до: <?=$stepData[$step]["maxW"]?>" name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].WIDTH" type="number" min='<?=$stepData[$step]["minW"]?>' max='<?=$stepData[$step]["maxW"]?>'  required />
								<div class="invalid-feedback invalW text-end">
								Пожалуйста, введите высоту изделия от: <?=$stepData[$step]["minW"]?> до: <?=$stepData[$step]["maxW"]?>
								</div>
								<div class="valid-feedback text-end">
								Данные введены верно.
								</div>
							</div>
							<div class="input-group my-1">
								<label>Количество</label>
								<input class="form-control calc-count" placeholder="от: 1" name="ITEM[<?=$_SESSION["CALC"]["IZDELIE"]?>].COUNT" min="1" max="100" type="number" step="1" value="1" required />
								<div class="invalid-feedback invalC text-end">
								Пожалуйста, введите количество изделий.
								</div>
								<div class="valid-feedback text-end">
								Данные введены верно.
								</div>
							</div>	
						</div>
						<?
						if ($_SESSION['CALC']['PRISADKA'] == 1) {
							?>
							<?}?>
						<a href="javascript:void(0)" class="text-danger del-item my-3" data-id="<?=$_SESSION['CALC']['IZDELIE']?>"><i class="bi bi-x mx-2"></i>Удалить</a>
						<!-- <a href="javascript:void(0)" class="text-success calculate mx-5 my-3" data-id="<?=$_SESSION['CALC']['IZDELIE']?>"><i class="bi bi-calculator mx-2"></i>Рассчитать</a> -->
						<div class="calc-result"></div>
				</div>

		</div>

<?
}
elseif ($step == 10) {
	//Расчет конкретного изделия
			$result = $square * $price * $count;

			$result = (int) ($result * 100) / 100; 

			?>
			<div>
				<ul>
					<li>Площадь изделия: <?=$square?></li>	

					<li>Цена за 1 м2: <?=$price?></li>	

					<li>Количество изделий: <?=$count?></li>

				</ul>
				Итого: <?=$result?> руб.
			</div><? 
			
}
elseif ($step == 11) {
	//Расчет всех изделий 
		?>
			<div id="step<?=$step?>" class="step-wrap row align-items-center">
				<div class="col text-start">
					Стоимость заказа: <?=$resultCost?> руб.
				</div>
				<div class="col">
					
				</div>
				<div class="col text-end">
					<a class="make-order btn btn-primary my-3" href="javascript:void(0)" data-id="11"><i class="bi bi-cart-check-fill mx-2"></i>Оформить заказ</a>
					<a id="cancel-calc-order" class="btn btn-danger my-3" href="javascript:void(0)">Отмена</a>
				</div>
			</div>
		<? 	
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php")?>
