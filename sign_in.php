<?php 
	require "db/connectt.php";
	$data = $_POST;

 	if (isset($data['reg']))
	{
		$eggor = array();
		
		if ($data['surname'] == '')
		{
			$eggor[] = "Введите фамилию";
		}

		if ($data['name'] == '')
		{
			$eggor[] = "Введите имя";
		}

		if ($data['series'] == '')
		{
			$eggor[] = "Введите серию паспорта";
		}

		if ($data['num'] == '')
		{
			$eggor[] = "Введите номер паспорта";
		} else {
			$resul = R::exec('SELECT id FROM `client` WHERE id = \''. $data['series'] . $data['num']. '\'');
			if (!(empty($resul)))
			$eggor[] = "Пользователь с таким паспортом уже существует";
			}
		if ($data['login'] == '')
		{
			$eggor[] = "Введите логин";
		}else if (R::count('client', "login = ?", array($data['login'])) >0)
			$eggor[] = "Такой пользователь уже существует";
		
		if ($data['passwd'] == '')
			{
			$eggor[] = "Некорректный пароль";
		} else 
		if ($data['passwd2'] == '' || $data['passwd'] != $data['passwd2'])
			{
			$eggor[] = "Некорректный повтор пароля";
			}	

		if (empty($eggor))	
		{
			R::exec('INSERT INTO `client` (`id`, `surname`, `name`, `patronymic`, `login`, `password`) 
			VALUES (\'' . $data['series'] . $data['num'] . '\', 
			\'' . $data['surname'] . '\', 
			\'' . $data['name']. '\', 
			\'' . $data['patr'] . '\', 
			\'' . $data['login'] . '\', 
			\'' . password_hash($data['passwd'], PASSWORD_DEFAULT) . '\')');
			
			$_SESSION['log_user'] = $data['series'] . $data['num'];
			$_SESSION['login'] = $data['login'];
 		 	header('location: /index.php');
		}
		else 
		foreach ($eggor as $key => $value) {
			echo $value . "</br>";
		}
	}

 ?>


<title>Регистрация</title>
<form action="sign_in.php" method="POST">
	<p>
		<p><strong>Фамилия</strong> </p>
		<input type="text" name="surname" pattern = "[А-Я]+([а-я]{1,24})" value="<?php echo @$data['surname']; ?>">
	</p>
	<p> 	
		<p> <strong>Имя</strong> </p>
		<input type="text" name="name" pattern = "[А-Я]+([а-я]{1,24})" value="<?php echo @$data['name']; ?>">
	</p>
	<p> 
		<p> <strong>Отчество</strong> </p>
		<input type="text" name="patr" pattern = "[А-Я]+([а-я]{1,24})" value="<?php echo @$data['patr']; ?>">
	</p>
	<p> 
		<p> <strong>Серия и номер паспорта</strong> </p>
		<input type="text" name="series" pattern = "[0-9]{4}" value="<?php echo @$data['series']; ?>">
		<input type="text" name="num" pattern = "[0-9]{6}" value="<?php echo @$data['num']; ?>">
	</p>
	<p>
		<p> <strong>Логин </strong> </p>
		<input type="text" name="login" pattern = "[A-Za-z0-9]{6,}" value="<?php echo @$data['login']; ?>">
	</p>

	<p>
		<p> <strong>Пароль</strong> </p>
		<input type="password" name="passwd" pattern = "[A-Za-z0-9]{6,}" value="<?php echo @$data['passwd']; ?>">
	</p>
	<p>
		<p> <strong>Подтвердите пароль</strong> </p>
		<input type="password" name="passwd2" value="<?php echo @$data['passwd2']; ?>">
	</p>

<p>
	<button type="submit" name = "reg">Зарегистрироваться</button>
</p>	
<p>
<a href="log_in.php">Вход </a></br></br>
<a href="index.php">Главная страница </a></p>
</p>

 </form>