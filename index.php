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
		
			// Изменение комнат и типов. 
		if (isset($data['change_rooms']))
		{
			$res = R::getAll("SELECT number FROM `room`");
			if ($res) 
			{
				echo "<form action='' method='POST'>
						Выберите комнату, которую необходимо изменить
					    <select  name='room'>";
				foreach ($res as $key => $value) 
				{
					echo "<option value= '" .  $value['number'] . "'>" . $value['number'] . "</option>";
				}
				echo "<p><input type='submit' name = 'change_choice_room' value='Выбрать'></p></select></form>";
			}
			else echo "Комнат нет";
			echo "</br>";
		}

		if (isset($data['change_choice_room']))
		{
			// Выбор 
			$res = R::getAll("SELECT `Количество мест`, `Тип`, `Состояние`, `Номер корпуса` FROM `комната` WHERE `Номер комнаты` = " . $data['room']);
			echo "Текущие характеристики комнаты №". $data['room'] .": ";
			print_table($res);
			echo "</br>Новые характеристики:</br>";

			$types = R::getAll("SELECT type.id, type.name FROM `type`");
			$states = R::getAll("SELECT `state`.`id`, `state`.`name` FROM `state` ORDER BY `state`.`id` DESC");

			echo "<form action='' method='POST'>
					<input type = 'hidden' name = 'room' value = '". $data['room'] ."'>
					Количество мест
					<input type='number' name = 'places' min = 1 max = 20> </br></br>
					Тип комнаты
					<select name='type_room'>";
			foreach ($types as $key => $value)
			{
				echo "<option value= '" .  $value['id'] . "'>" . $value['name'] . "</option>";
			}
			echo "</select></br></br>
					Состояние
					<select name='state'>";
			foreach ($states as $key => $value)
			{
				echo "<option value= '" .  $value['id'] . "'>" . $value['name'] . "</option>";
			}
			echo "</select>
					<p><input type='submit' name = 'change_rooms_atr' value='Сохранить изменения'></p></form>";
		}
		if (isset($data['change_rooms_atr']))
		{
			if ($data['places'] == '') 
			{				
				$res = R::getAll("SELECT `Количество мест`, `Тип`, `Состояние`, `Номер корпуса` FROM `комната` WHERE `Номер комнаты` = " . $data['room']);
				echo "Текущие характеристики комнаты №". $data['room'] .": ";
				print_table($res);
				echo "</br>Введите номер комнаты</br>Новые характеристики:</br>";

				$types = R::getAll("SELECT type.id, type.name FROM `type`");
				$states = R::getAll("SELECT `state`.`id`, `state`.`name` FROM `state` ORDER BY `state`.`id` DESC");

				echo "<form action='' method='POST'>
						<input type = 'hidden' name = 'room' value = '". $data['room'] ."'>
						Количество мест
						<input type='number' name = 'places' min = 1 max = 20> </br></br>
						Тип комнаты
						<select name='type_room'>";
				foreach ($types as $key => $value)
				{
					echo "<option value= '" .  $value['id'] . "'>" . $value['name'] . "</option>";
				}
				echo "</select></br></br>
						Состояние
						<select name='state'>";
				foreach ($states as $key => $value)
				{
					echo "<option value= '" .  $value['id'] . "'>" . $value['name'] . "</option>";
				}
				echo "</select>
					<p><input type='submit' name = 'change_rooms_atr' value='Сохранить изменения'></p></form>";
			}
			else
			{
				R::exec("UPDATE `room` 
					SET `places` = '". $data['places'] ."',
					type = ". $data['type_room'] .",
					room.state = ".  $data['state'] ."
					WHERE `room`.`number` = " . $data['room']);
				echo "Комната №". $data['room'] ." изменена.";
			}
		}

		if (isset($data['change_types']))
		{
			$res = R::getAll("SELECT type.id AS 'Номер типа', type.name AS 'Тип', type.cost AS 'Цена' FROM `type`");
			echo "Доступные типы: ";
			print_table($res);
			echo "</br>";

			echo "<form action='' method='POST'>
					Выберите тип 
					<select name='type'>";
			foreach ($res as $key => $value)
			{
				echo "<option value= '" .  $value['Номер типа'] . "'>" . $value['Тип'] . "</option>";
			}
			echo "</select>
					<p><input type='submit' name = 'choice_type' value='Выбрать'></p></form>";
		}
		
		if (isset($data['choice_type']))
		{
			$res = R::getAll("SELECT type.name, type.cost FROM `type` WHERE id = " . $data['type']);
			$res = $res[0];
			echo "Вы выбрали тип " . $res['name'] . ". Текущая цена за день - " . $res['cost'] . ".</br></br>";
			echo "<form action='' method='POST'>
				<input type = 'hidden' name = 'type' value = '". $data['type'] ."'>
				Новая цена 
				<input type='number' name = 'cost' min = 100 max = 1000000> </br>
				<p><input type='submit' name = 'save_cost' value='Сохранить изменения'></p>
				</form>";		
		}

		if (isset($data['save_cost']))
		{
			if ($data['cost'] == '') 
			{
				$res = R::getAll("SELECT type.name, type.cost FROM `type` WHERE id = " . $data['type']);
				$res = $res[0];
				echo "Вы выбрали тип " . $res['name'] . ". Текущая цена за день - " . $res['cost'] . ".</br></br>";
				echo "<form action='' method='POST'>
					<input type = 'hidden' name = 'type' value = '". $data['type'] ."'>
					Введите новую цену. </br>
					Новая цена 
					<input type='number' name = 'cost' min = 100 max = 1000000> </br>
					<p><input type='submit' name = 'save_cost' value='Сохранить изменения'></p>
					</form>";	
			}
			else
			{
				R::exec("UPDATE `type` SET `cost` = '". $data['cost'] ."' WHERE `type`.`id` = " . $data['type']);
				echo "Цена типа изменена.";
			}
		}
		// Удаление всякого.
		if (isset($data['delete_building']))
		{
			$res = R::getAll("SELECT id FROM `building`ORDER BY `building`.`id` ASC");
			if ($res)
			{
			echo "<form action='' method='POST'>
						После удаления все комнаты корпуса также пропадут. Будьте осторожны. </br></br>
						Выбрать корпус
					    <select  name='building'>";
			foreach ($res as $key => $value) 
			{
				echo "<option value= '" .  $value['id'] . "'>" . $value['id'] . "</option>";
			}
			echo "<p><input type='submit' name = 'delete_choice_building' value='Удалить'></p></select></form>";
			} else echo "Корпусов нет";
		}

		if (isset($data['delete_choice_building']))
		{
			R::exec("DELETE FROM `building` WHERE `building`.`id` = " . $data['building']);
			echo "Корпус удалён";
		}

		if (isset($data['delete_rooms']))
		{
			$res = R::getAll("SELECT number FROM `room` ORDER BY number ASC");
			if ($res)
			{
				echo "<form action='' method='POST'>
							После удаления комнату уже не вернуть. Будьте осторожны. </br></br>
							Выбрать комнату
						    <select  name='room'>";
				foreach ($res as $key => $value) 
				{
					echo "<option value= '" .  $value['number'] . "'>" . $value['number'] . "</option>";
				}
				echo "<p><input type='submit' name = 'delete_choice_room' value='Удалить'></p></select></form>";
			} else echo "Комнат нет";
		}

		if (isset($data['delete_choice_room']))
		{
			R::exec("DELETE FROM `room` WHERE `room`.`number` = " . $data['room']);
			echo "Комната удалена";
		}

		if (isset($data['delete_ticket']))
		{
			$res = R::getAll("SELECT id FROM `ticket` ORDER BY id ASC");
			if ($res)
			{
				echo "<form action='' method='POST'>
							После удаления путёвку уже не вернуть. Будьте осторожны. </br></br>
							Выбрать путёвку
						    <select  name='ticket'>";
				foreach ($res as $key => $value) 
				{
					echo "<option value= '" .  $value['id'] . "'>" . $value['id'] . "</option>";
				}
				echo "<p><input type='submit' name = 'delete_choice_ticket' value='Удалить'></p></select></form>";
			} else echo "Путёвок нет";
		}

		if (isset($data['delete_choice_ticket']))
		{
			R::exec("DELETE FROM `ticket` WHERE `ticket`.`id` = " . $data['ticket']);
			echo "Путёвка удалена";
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