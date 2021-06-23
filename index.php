<?php 
	require "db/connectt.php"; 
?>
<center>	
	</br>
<?php 
	if ($_SESSION['log_user'] > 0) 
	{
		$res = R::getAll("SELECT client.id, client.surname, client.name, client.patronymic FROM `client` WHERE  client.login = '" . $_SESSION['login'] . "'");

		$id = $res[0]['id'];

		echo "Здравствуйте, ". $res[0]['name'] . " ". $res[0]['patronymic'] . ". </br></br>";

		$data = $_POST;
		
		// Обработка кнопок
		if (isset($data['choice']))
		{
			// Новая путёвка. 
			print_date();
		}
		
		if (isset($data['get_rooms']))
		{
			// Выбор комнаты. 
			if (!($data['date_begin'])) 
			{
				echo "Введите дату заезда";
				print_date();
			}
			else if (!($data['date_end'])) 
				{
					echo "Введите дату выезда";
					print_date();
				}
				else if ($data['date_end'] < $data['date_begin'])
				{
					echo "Дата выезда должна быть позже даты заезда";
					print_date();
				}
				else
				{
					$resul = R::getAll("SELECT * FROM `корпус`");

					echo "Адреса корпусов:";
					print_table($resul);

					$begin = $data['date_begin'];
					$end = $data['date_end'];

					$resul = get_rooms($begin, $end);

					$days = 1+(strtotime($end) - strtotime($begin))/60/60/24;
					echo "</br>Доступные комнаты в период с " . $begin . " по " . $end . " (количествно дней - " . $days . "):";
					print_table($resul);
					echo "</br>"; 
					
					print_select_room($resul, $begin, $end);			
				}
		}

		if (isset($data['choice_room']))
		{
			// Добавление путёвки.
			R::exec("INSERT INTO `ticket` (`id`, `client`, `room`, `check_date`, `eviction_date`) 				VALUES (NULL, '". $id . "', '". $data['rooms']. "', '". $data['begin'] . "', '". $data['end']. "')");
			echo "Путёвка куплена";
		}

		if (isset($data['change']))
		{
			// Изменение имени.
			change_name_print();
		}

		if (isset($data['change_name']))
		{
			// Изменение имени. 
			$resul = R::getAll("SELECT id FROM `client` WHERE id = '". $data['series'] . $data['num']. "'");
			if (empty($resul))
			{
				R::exec("UPDATE `client` 
				SET `id` = '" . $data['series'] . $data['num'] . "', 
				`surname` = '" .$data['surname'] . "',   
				`name` = '" . $data['name'].	 "',   
				`patronymic` = '" . $data['patr']. "'  
				WHERE `client`.`id` = '" . $id. "'");
			} 
			else 
			{
				change_name_print();
				echo "Пользователь с таким паспортом уже существует";
			}
		}
		if (isset($data['list']))
		{
			// Вывод путёвок. 
			$res = R::getAll('SELECT ticket.id, ticket.room, ticket.check_date, ticket.eviction_date FROM `ticket` WHERE client =' . $id);
			
			if (!(empty($res)))
			{
				echo "Список ваших путёвок</br></br>";
				echo "<table>
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
    		<tr><td>Номер путёвки</td><td>Комната</td><td>Дата заезда</td><td>Дата выезда</td><td>Цена</td><td>Состояние</td></tr>";
    		$cnt = 0;
				foreach ($res as $key => $value) 
			 	{
			 		$cost = R::getAll('SELECT `цена путёвки`.`Стоимость проживания`,`цена путёвки`.`Статус путёвки`  FROM `цена путёвки` WHERE `Номер путёвки` = ' . $value['id']);

			 		echo "<tr><td>" . $value['id'] . "</td><td>" . $value['room'] . "</td><td>" . $value['check_date'] . 
			 		"</td><td>" . $value['eviction_date'] . "</td><td>" . $cost[0]['Стоимость проживания'] . "</td><td>" . $cost[0]['Статус путёвки'] . "</td></tr>";
			 		if ($cost[0]['Статус путёвки'] == "Ожидается") $cnt++;
				}
				echo "</table></br></br>";

				$res = R::getAll('SELECT ticket.id FROM `ticket` WHERE ticket.check_date > NOW() AND client =' . $id);
				
				if ($cnt > 0)
				{
					echo "<form action='' method='POST'>
						Изменить путёвку
					    <select  name='tickets'>";

					foreach ($res as $key => $value) 
				 	{
				 		echo "<option value= '" .  $value['id'] . "'>" . $value['id'] . "</option>";
					}
					echo "<p><input type='submit' name = 'choice_ticket' value='Выбрать'></p></select></form>";
				}
			}
			else echo "К сожалению, вы ещё не были у нас. </br>Срочно это исправьте.</br></br>";
		}
		
				if (isset($data['choice_ticket']))
		{
			// Изменение путёвки. 
			echo "<form action='' method='POST'>
					Выберите, что нужно сделать с путёвкой №".  $data['tickets']. ":</br></br>
					<input type='hidden' name = 'choiced_tickets' value = '". $data['tickets'] . "'>
					<button type='submit' name = 'change_date'>Изменить даты</button>
					<button type='submit' name = 'change_room'>Изменить комнату</button>
					<button type='submit' name = 'delete_ticket'>Отменить путёвку</button>
				    </form>";
		}

		if (isset($data['change_date']))
		{
			// Изменение даты путёвки. 
			print_date_change();
		}

		if (isset($data['change_date_tic']))
		{
			// Изменение даты путёвки. 
			if (!($data['date_begin'])) 
			{
				echo "Введите дату заезда";
				print_date_change();
			}
			else if (!($data['date_end'])) 
				{
					echo "Введите дату выезда";
					print_date_change();
				}
				else if ($data['date_end'] < $data['date_begin'])
				{
					echo "Дата выезда должна быть позже даты заезда";
					print_date_change();
				}
				else
				{
				$tic = R::getAll("SELECT * FROM `ticket` WHERE id = " . $data['choiced_ticket']);
				$tic = $tic[0];

				R::exec("DELETE FROM `ticket` WHERE `ticket`.`id` = " . $data['choiced_ticket']);

				$begin = $data['date_begin'];
				$end = $data['date_end'];

				$resul = get_rooms($begin, $end);
			
			$f = 0;
			foreach ($resul as $key => $value) {
				if ($value['Номер'] ==  $tic['room']) 
					$f = 1;
			}

			if ($f == 1)
			{
			// Если всё ок, изменение дат.
				R::exec("INSERT INTO `ticket` (`id`, `client`, `room`, `check_date`, `eviction_date`) 
				VALUES ('". $tic['id'] ."', '" . 
				$tic['client'] ."', '" . 
				$tic['room'] . "', '" . 
				$begin . "', '".
				$end  ."')");
				echo "Даты изменены";
			}
			else 
			{
				// если не всё ок, то "выберите другую комнату"
				R::exec("INSERT INTO `ticket` (`id`, `client`, `room`, `check_date`, `eviction_date`) 
				VALUES ('". $tic['id'] ."', '" . 
				$tic['client'] ."', '" . 
				$tic['room'] . "', '" . 
				$tic['check_date'] . "', '".
				$tic['eviction_date']  ."')");
				echo "Комната в эти даты занята. Выберите другую:";
				print_table($resul);
				echo "</br>";
				print_select_room_change($resul, $begin, $end, $tic);
					
			}
		}
		}
		if (isset($data['choice_change_room']))
		{
			// Изменение комнаты путёвки при неподходящей дате.
			R::exec("UPDATE `ticket` 
				SET room = " . $data['rooms'] . ", 
				check_date = '" . $data['begin']. "', 
				eviction_date = '" . $data['end']. "' WHERE id = " . $data['tic']);
			echo "Путёвка изменена";
		}

		if (isset($data['change_room']))
		{
			// Изменение комнаты путёвки.

			$tic = R::getAll("SELECT * FROM `ticket` WHERE id = " . $data['choiced_tickets']);
			$tic = $tic[0];

			$resul = get_rooms($begin, $end);
			echo "</br>Доступные комнаты:";
			print_table($resul);
			echo "</br>"; 
					
			// Вывести список свободных комнат.
			print_select_change_room($resul, $tic['id']);;
		}

		if (isset($data['change_ticket_room']))
		{
			// Изменение комнаты в путёвке.
			R::exec("UPDATE `ticket` SET room = " . $data['rooms'] . " WHERE id = " . $data['tic']);
			echo "Путёвка изменена";
		}

		if (isset($data['delete_ticket']))
		{
			// Удаление путёвки. 
			R::exec("DELETE FROM `ticket` WHERE `ticket`.`id` = " . $data['choiced_tickets']);
			echo "Путёвка отменена";
		}

		write_button();

		echo "<a href='log_out.php'>Выйти</a></br></br>";
		
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
		
		if (isset($data['delete_city']))
		{
			$res = R::getAll("SELECT id, name FROM `city` ORDER BY id ASC");
			if ($res)
			{
				echo "<form action='' method='POST'>
							После удаления исчезнут все улицы и корпуса данного города. Будьте осторожны. </br></br>
							Выбрать город
						    <select  name='city'>";
				foreach ($res as $key => $value) 
				{
					echo "<option value= '" .  $value['id'] . "'>" . $value['name'] . "</option>";
				}
				echo "<p><input type='submit' name = 'delete_choice_city' value='Удалить'></p></select></form>";
			} else echo "Городов нет";
		}

		if (isset($data['delete_choice_city']))
		{
			R::exec("DELETE FROM `city` WHERE `city`.`id` = " . $data['city']);
			echo "Город удалён";
		}

		if (isset($data['delete_street']))
		{
			$res = R::getAll("SELECT street.id, street.name, city.name AS 'city' FROM `street`, city WHERE street.city = city.id");
			if ($res)
			{
				echo "<form action='' method='POST'>
							После удаления исчезнут все корпуса данной улицы. Будьте осторожны. </br></br>
							Выбрать улицу
						    <select  name='street'>";
				foreach ($res as $key => $value) 
				{
					echo "<option value= '" .  $value['id'] . "'>" . $value['name'] . "(". $value['city'] .")</option>";
				}
				echo "<p><input type='submit' name = 'delete_choice_street' value='Удалить'></p></select></form>";
			} else echo "Улиц нет";
		}

		if (isset($data['delete_choice_street']))
		{
			R::exec("DELETE FROM `street` WHERE `street`.`id` = " . $data['street']);
			echo "Улица удалён";
		}

		write_admin_button();
		echo "<a href='log_out.php'>Выйти</a></br></br>";
		
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

function print_table($table)
{
	$f = 0;
	echo "<table>
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
    		</style>";
	foreach ($table as $key => $value) 
	{
		if ($f==0) 
		{
			echo "<tr>";
			foreach ($value as $name => $val)
				echo "<td>" .  $name . "</td>";
			echo "</tr>";
			$f = 1;
		}
		echo "<tr>";
		foreach ($value as $name => $val)
		{
			echo "<td>" .  $val . "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}

function print_date()
{
	echo "<form action='' method='POST'>
			Дата заезда
			<input type = date name = date_begin min = " . date("Y-m-d") ." value = " . $_POST['date_begin']. "></br></br>
			Дата выезда
			<input type = date name = date_end min = " . date("Y-m-d") . " value = " . $_POST['date_end']. "> </br><br>
			<button type='submit' name = 'get_rooms'>Свободные номера в этот период</button>
			</form>";
}

function change_name_print()
{
	echo "<form action='' method='POST'>
				ФИО должно состоять из русских букв, начинаться с заглавной, не содержать пробелов и чисел
				</br>Фамилия</br>
				<input type='text' name='surname' pattern = '[А-Я]+([а-я]{1,24})' value='". $res[0]['surname'] . "'>
				</br>Имя</br>	
					<input type='text' name='name' pattern = '[А-Я]+([а-я]{1,24})' value='". $res[0]['name'] . "'>
				</br>Отчество</br>
					<input type='text' name='patr' pattern = '[А-Я]+([а-я]{1,24})'' value='" . $res[0]['patronymic'] . "''>
				</br>Серия и номер паспорта</br>
					<input type='text' name='series' pattern = '[0-9]{4}'' value='" . $data['series'] . "''>
					<input type='text' name='num' pattern = '[0-9]{6}' value='" . $data['num'] . "'>
				</p>
				<button type='submit' name = 'change_name'>Обновить данные</button>
				</form>";
}

function print_date_change()
{
	if (!($_POST['choiced_tickets'])) $_POST['choiced_tickets'] = $_POST['choiced_ticket'];
	echo "<form action='' method='POST'>
			Выберите новые даты</br>
			Дата заезда
			<input type='hidden' name = 'choiced_ticket' value = '". $_POST['choiced_tickets'] . "'>
			<input type = date name = date_begin min = " . date("Y-m-d") ." value = " . $_POST['date_begin']. "></br></br>
			Дата выезда
			<input type = date name = date_end min = " . date("Y-m-d") . " value = " . $_POST['date_end']. "> </br></br>
			<button type='submit' name = 'change_date_tic'>Изменить даты путёвки</button>
			</form>";
}

function print_select_room($table, $begin, $end)
{
	echo "<form action='' method='POST'>
	<input type='hidden' name = 'begin' value = '".$begin . "'>
	<input type='hidden' name = 'end' value = '".$end . "'>
	Выбрать комнату:
    <select  name='rooms'>";
    foreach ($table as $key => $value) 
    {
    	echo "<option value= '" .  $value['Номер'] . "'>" . $value['Номер'] . "</option>";
    }
   	echo "<p><input type='submit' name = 'choice_room' value='Выбрать'></p></select></form>";
}

function print_select_room_change($table, $begin, $end, $tic)
{
	echo "<form action='' method='POST'>
	<input type='hidden' name = 'begin' value = '". $begin . "'>
	<input type='hidden' name = 'end' value = '". $end . "'>
	<input type='hidden' name = 'tic' value = '". $tic['id'] . "'>
	<input type='hidden' name = 'client' value = '". $tic['client'] . "'>
	Выбрать комнату:
    <select  name='rooms'>";
    foreach ($table as $key => $value) 
    {
    	echo "<option value= '" .  $value['Номер'] . "'>" . $value['Номер'] . "</option>";
    }
   	echo "<p><input type='submit' name = 'choice_change_room' value='Выбрать'></p></select></form>";
}

function print_select_change_room($table, $id)
{
	echo "<form action='' method='POST'>
	<input type='hidden' name = 'tic' value = '". $id . "'>
	Выбрать комнату:
    <select  name='rooms'>";
    foreach ($table as $key => $value) 
    {
    	echo "<option value= '" .  $value['Номер'] . "'>" . $value['Номер'] . "</option>";
    }
   	echo "<p><input type='submit' name = 'change_ticket_room' value='Выбрать'></p></select></form>";
}

function get_rooms($begin, $end)
{
return R::getAll("SELECT room.building AS 'Корпус', `tv`.num AS 'Номер', type.name AS 'Тип', type.cost AS 'Цена за ночь' FROM room, 
										type,
										(SELECT
										room.number AS num
										FROM
										room
										WHERE
										!(
										room.number IN(
										(
										SELECT
										ticket.room AS 'Комната'
										FROM
										ticket
										WHERE
										(
										check_date <= '". $begin. "' AND eviction_date >= '". $begin. "'
										) OR(
										check_date <= '" . $end . "' AND eviction_date >= '" . $end . "'
										) OR(
										check_date >= '". $begin. "' AND eviction_date <= '" . $end . "'
										)
										)
										)
										) AND room.state <> 1
										UNION
										SELECT room.number FROM
										room,
										(SELECT
										ticket.room AS 'Комната',
										SUM(1) AS 'Места'
										FROM
										ticket
										WHERE
										(
										check_date <= '". $begin. "' AND eviction_date >= '". $begin. "'
										) OR(
										check_date <= '" . $end . "' AND eviction_date >= '" . $end . "'
										) OR(
										check_date >= '". $begin. "' AND eviction_date <= '" . $end . "'
										)
										GROUP BY ticket.room) AS places
										
										WHERE places.`Комната` = room.number AND (room.places - places.`Места` >0)) AS tv
										WHERE room.number = tv.num AND room.type = type.id ORDER BY tv.num ASC");
}

?>
</center>
<title>Пансионат</title>