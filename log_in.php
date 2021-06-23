<?php 
	require "db/connectt.php";
	$data = $_POST;
	if (isset($data['in']))
	{
		if ($data['login'] == 'admin')
		{

			$eggor = array();
			if ($data['passwd'] == 'kfreue34kv!mmcl')
			{
				$_SESSION['log_user'] = -1;	
				$_SESSION['login'] = 'admin';
  				header('location: index.php');
			}
			else 
			{
				$eggor[] = "Пароль не подходит";
			}
			if (!(empty($eggor)))
			foreach ($eggor as $key => $value) {
				echo $value . "</br>";
		}
		}
		else
		{
		$eggor = array();
		if (trim($data['login']) == '')
		{
			$eggor[] = "Введите логин";
		} else
		if (trim($data['passwd']) == '')
		{
			$eggor[] = "Введите пароль";
		} 
		$user = R::findOne('client', 'login = ?', array($data['login']));

		if ($user)	
		{
			if (password_verify($data['passwd'], $user->password))
			{
				$_SESSION['log_user'] = $user->id;	
				$_SESSION['login'] = $data['login'];
  				header('location: index.php');
  				echo "Поздравляем, вы есть " . $_SESSION['log_user'];
  				exit;
			}else $eggor[] = "Пароль не подходит";
		}
		else 
		{
			$eggor[] = "Пользователь не найден";
		}
		if (!(empty($eggor)))
		foreach ($eggor as $key => $value) {
			echo $value . "</br>";
		}
	}
	}
?>

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
<a href="index.php">Главная страница</a></p>
 </form>