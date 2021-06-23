<?php 
	require "db/connectt.php"; 
?>
<center>	
	</br>
<?php 
	if ($_SESSION['log_user'] > 0) 
	{
		
	}else if ($_SESSION['log_user'] == -1) 
	{
		echo "Вы вошли как администратор.</br></br>";

		$data = $_POST;
	}else
	{
		// Список пансионатов.
		// Для заказа билетов необходима регистрация.
		echo "Добро пожаловать на сайт пансионата</br></br>
		Для заказа путёвки необходима регистрация</br></br>
			<a href='log_in.php'>Вход</a></br></br>
			<a href='sign_in.php'>Регистрация</a>";
	}
?>

<?php function write_admin_button()
{
	echo "<form action='' method='POST'>
	<button type='submit' name = 'get_building'>	Получить список корпусов</button>
	<button type='submit' name = 'get_client'>		Получить список клиентов сейчас</button>
	<button type='submit' name = 'get_list_rooms'>	Получить список комнат</button>
	<button type='submit' name = 'get_ticket'>		Получить список путёвок</button>
	</br></br>
	<button type='submit' name = 'get_list_rooms_building'>	Получить список комнат в корпусе</button>
	<button type='submit' name = 'get_ticket_date'>			Получить список путёвок между датами</button>
	</br></br></br>
	<button type='submit' name = 'add_building'>	Добавить корпус</button>
	<button type='submit' name = 'add_rooms'>		Добавить комнаты</button>
	<button type='submit' name = 'add_ticket'>		Добавить путёвку постояльцу</button>
	</br></br>
	<button type='submit' name = 'change_rooms'>Изменить комнаты</button>	
	<button type='submit' name = 'change_types'>Изменить цену типа</button>		
	</br></br></br>
	<button type='submit' name = 'delete_building'>	Удалить корпус</button>		
	<button type='submit' name = 'delete_rooms'>	Удалить комнату</button>	
	<button type='submit' name = 'delete_ticket'>	Аннулировать путёвку</button>	
	</br></br>
	<button type='submit' name = 'delete_city'>		Удалить город</button>		
	<button type='submit' name = 'delete_street'>	Удалить улицу</button>	
	</br>
	</form>";
	// Добавить пользователя.

	// Просмотреть почти всё.
}
?>
</center>
<title>Пансионат</title>