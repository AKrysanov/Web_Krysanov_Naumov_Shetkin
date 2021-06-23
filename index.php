<?php 
	require "db/connectt.php"; 
?>
<center>	
	</br>
<?php 
	if ($_SESSION['log_user'] > 0) 
	{
		
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
</center>
<title>Пансионат</title>