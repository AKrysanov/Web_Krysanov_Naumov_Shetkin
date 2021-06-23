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
		
		if (isset($data['get_building']))
		{
			$res = R::getAll("SELECT * FROM `корпус`");
			if ($res)
			{
				echo "Список корпусов:";
				print_table($res);
			}
			else echo "Корпусов нет";
			echo "</br>";
		}

		if (isset($data['get_client']))
		{
			$res = R::getAll("SELECT * FROM `клиенты сейчас`");
			if ($res) 
			{
				echo "Список клиентов в настоящее время:";
				print_table($res);
			}
			else echo "Сейчас клиентов нет";
			echo "</br>";
		}

		if (isset($data['get_list_rooms']))
		{
			$res = R::getAll("SELECT * FROM `комната`");
			if ($res) 
			{
				echo "Список комнат:";
				print_table($res);
			}
			else echo "Комнат нет";
			echo "</br>";
		}

		if (isset($data['get_ticket']))
		{
			$res = R::getAll("SELECT * FROM `путёвки`");
			if ($res) 
			{
				echo "Список путёвок:";
				print_table($res);
			}
			else echo "Путёвок нет";
			echo "</br>";
		}

		if (isset($data['get_list_rooms_building']))
		{
			$res = R::getAll("SELECT id FROM `building`ORDER BY `building`.`id` ASC");
			echo "<form action='' method='POST'>
						Выбрать корпус
					    <select  name='building'>";
			foreach ($res as $key => $value) 
			{
				echo "<option value= '" .  $value['id'] . "'>" . $value['id'] . "</option>";
			}
			echo "<p><input type='submit' name = 'get_rooms_building' value='Выбрать'></p></select></form>";
		}

		if (isset($data['get_rooms_building']))
		{
			
			$res = R::getAll("SELECT `Номер комнаты`, `Количество мест`, `Тип`, `Цена`, `Состояние` FROM `комната` WHERE `комната`.`Номер корпуса` = " . $data['building']);
			if ($res)
			{
				echo "Список комнат в корпусе №" . $data['building'];
				print_table($res);
			}
			else echo "В корпусе №" . $data['building'] . " нет комнат. Вы можете их добавить.";
			echo "</br>";
		}

		if (isset($data['get_ticket_date']))
		{
			print_date_admin();
		}
		
		if (isset($data['get_tickets']))
		{
			if (!($data['date_begin'])) 
			{
				echo "Введите дату заезда";
				print_date_admin();
			}
			else if (!($data['date_end'])) 
				{
					echo "Введите дату выезда";
					print_date_admin();
				}
				else if ($data['date_end'] < $data['date_begin'])
				{
					echo "Дата выезда должна быть позже даты заезда";
					print_date_admin();
				}
				else
				{
					$res = R::getAll("SELECT * FROM `путёвки` WHERE `путёвки`.`Дата заезда` >= '". $data['date_begin'] ."' AND `путёвки`.`Дата выезда` <= '". $data['date_end'] ."'");
					if ($res)
					{
						echo "Список путёвок между " . $data['date_begin'] . " и " . $data['date_end'];
						print_table($res);
					}
					else echo "Между " . $data['date_begin'] . " и " . $data['date_end'] ." нет посетителей";
					echo "</br>";
				}
		}
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

function print_date_admin()
{
	echo "<form action='' method='POST'>
			Дата заезда
			<input type = date name = date_begin value = " . $_POST['date_begin']. "></br></br>
			Дата выезда
			<input type = date name = date_end value = " . $_POST['date_end']. "> </br>
			<button type='submit' name = 'get_tickets'>Получить список путёвок</button>
			</form>";
}

?>
</center>
<title>Пансионат</title>