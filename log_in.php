<title>Вход</title>
<form action="log_in.php" method="POST">

	<p> 
		<p> <strong>Логин</strong> </p>
		<input type="text" name="login" value="<?php echo @$data['login']; ?>">
	</p>
	<p>
		<p> <strong>Пароль</strong> </p>
		<input type="password" name="passwd">
	</p>

<p>
	<button type="submit" name="in">Войти</button>
</p>	
<p>
<a href="sign_in.php">Регистрация</a></p>
<a href="dich.php">Главная страница</a></p>
 </form>