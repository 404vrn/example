<?
define('PUBLIC_AJAX_MODE', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].'/local/ajax/auth/smsc_api.php');

global 	$APPLICATION, 
		$USER;

if ($_REQUEST['sendCode']=="yes"){

	if ($_REQUEST['getPhone'] !=""){

		$getPhone = $_REQUEST['getPhone'];

		$phone = \Bitrix\Main\UserPhoneAuthTable::normalizePhoneNumber($getPhone);
		
		$PhoneAuthTable = \Bitrix\Main\UserPhoneAuthTable::getList(

				[
					'filter'=>
					[
						'PHONE_NUMBER' => $phone
					]
				]
			);
		
		while($user = $PhoneAuthTable->fetch()){

			$userId = $user['USER_ID'];
		}

		$_SESSION['phone_auth']['USER_ID'] = $userId;
		
		if ($userId){

			list($code, $phoneNumber) = \CUser::GeneratePhoneCode($userId);

			$sms = new \Bitrix\Main\Sms\Event(

			'SMS_USER_CONFIRM_NUMBER', // SMS_USER_RESTORE_PASSWORD

				[
					'USER_PHONE' => $phoneNumber,
					'CODE' => $code,
				]
			);
			
			$msg = "Код авторзаци: ".$code;

			ob_start();

			send_sms($phoneNumber,$msg);

			ob_end_clean();
		}

		else{

			echo 'Пользователь с таким номером не обнаружен';
		}	
	}

	else{

		echo "Поле не должно быть пустым";
	}	
}
	if ($_REQUEST['confirmCode']=='yes'){ 

		$getCode = $_REQUEST['getCode'];

		$userId = $_SESSION['phone_auth']['USER_ID'];

		$userPhone = \Bitrix\Main\UserPhoneAuthTable::getList([
		    'filter' => [

			'=USER_ID' => $userId

		    ],

		    'select' => ['USER_ID', 'PHONE_NUMBER', 'USER.ID', 'USER.ACTIVE'],
			
			])->fetchObject();

		if(!$userPhone) {

			echo 'Пользователь с таким номером не найден';
		}

		else{

			if(CUser::VerifyPhoneCode($userPhone->getPhoneNumber(), $getCode)) {
				
				if(!$USER->IsAuthorized()) {

					$USER->Authorize($userId);
					
					echo 'OK';
				}
			
			return true;

			}

			echo 'Код введен неверно';
		}
	}
?>
