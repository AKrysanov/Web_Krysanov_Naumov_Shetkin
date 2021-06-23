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