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
		//вывод информации
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
		//Добавление
		if (isset($data['add_rooms']))
		{
			echo "Выберите корпус и введите количетсво добавляемых комнат(1-20)</br></br>";

			$res = R::getAll("SELECT id FROM `building`ORDER BY `building`.`id` ASC");
			echo "<form action='' method='POST'>
						Выбрать корпус
					    <select  name='building'>";
			foreach ($res as $key => $value) 
			{
				echo "<option value= '" .  $value['id'] . "'>" . $value['id'] . "</option>";
			}
			echo "</select>
				<p> Количество добавляемых комнат
				<input type='number' min = 1 max = 20 name = 'count_rooms'></p>
				<p><input type='submit' name = 'adding_room' value='Выбрать'></p>
				</form>";
		}

		if (isset($data['adding_room']))
		{
			if ($data['count_rooms'] == '') $cnt = 1;
			else $cnt = $data['count_rooms'];

			$max = R::getAll("SELECT MAX(room.number) AS 'max' FROM `room` WHERE room.building = ". $data['building']);
			$max = $max[0]['max'] % 1000;

			echo "<form action='' method='POST'>
			<input type = 'hidden' name = 'building' value = '". $data['building'] ."'>
			<input type = 'hidden' name = 'count' value = '". $cnt ."'>
			<input type = 'hidden' name = 'max' value = '". $max  ."'>
			<table>
			<style>
		        	table 
		        	{
		            	border: solid 1px; 
		            	border-collapse: collapse;
		        	}	
		        	TD, TH {
					    padding: 3px; 
		   				border: 1px solid black; 
		   			}	
		    		</style>
		    		<tr><td>Номер комнаты</td><td>Количество мест</td><td>Тип</td><td>Состояние</td></tr>";

		    $type = R::getAll("SELECT id, name FROM `type`");
			$select_type = "";
			foreach ($type as $key => $value)
				$select_type = $select_type . "<option value= '" .  $value['id'] . "'>" . $value['name'] . "</option>";

			$states = R::getAll("SELECT `state`.`id`, `state`.`name` FROM `state` ORDER BY `state`.`id` DESC");
			$select_state = "";
			foreach ($states as $key => $value)
				$select_state = $select_state . "<option value= '" .  $value['id'] . "'>" . $value['name'] . "</option>";

			for ($i=0; $i < $cnt; $i++) 
			{ 
				echo "<tr><td>" . (1000*$data['building'] + $i + $max+1) . "</td>
				<td><input type='number' min = 1 max = 20 name = 'place". ($i+1) ."' value = 1></td>
				<td><select name='type". ($i+1) ."'>" . $select_type . "</select></td>
				<td><select name='state". ($i+1) ."'>" . $select_state . "</select></td>";
				echo "</tr>";
			}
			echo "</table>
			<p><input type='submit' name = 'adding_all_rooms' value='Добавить комнаты'></p>
			</form>";
		}

		if (isset($data['adding_all_rooms']))
		{
			for ($i=0; $i < $data['count']; $i++) 
			{ 
				if ($data['place' . ($i+1)] == '') $data['place' . ($i+1)] = 1;
				R::exec("INSERT INTO `room` (`number`, `building`, `places`, `type`, `state`) 
					VALUES ('". (1000*$data['building'] + $i + $data['max']+1) ."',
					'". $data['building'] ."',
					'". $data['place' . ($i+1)] ."',
					'". $data['type' . ($i+1)] ."',
					'". $data['state' . ($i+1)] ."')");
			}
			echo "Комнаты добавлены в корпус №" . $data['building'];
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