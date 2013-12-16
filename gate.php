<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
    <head>
        <title>Enter Nickname - Live chat</title>
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        <link type="text/css" rel="stylesheet" href="views/style/global.css" />
        <link type="text/css" rel="stylesheet" href="views/style/gate.css" />
    </head>
    <body>
    
    <form action="index.php" method="post" id="login">
    <p>If you are a member............</p>
    	<table>
   			<tr>
   				<td>
					<label>Username:</label>
				</td>
				<td> 
					<input type="text" name="username" id="username" />
        		</td>
       		</tr>
        	<tr> 
        		<td>
            		<label>Password:</label> 
        		</td>
        		<td>
        			<input type="password" name="password" />
        		</td>
        	</tr>
        	<tr>
        	<td />
        		<td id="loginDeksia">
        			<input type="submit" value="Login" class="submit" />
        		</td>
        	</tr>
        </table>
        <p>..or chat using a temporary nickname..</p>
        <table>
        	<tr>
        		<td>
        			<label>Nickname:</label>
        		</td>
        		<td>
        			<input type="text" name="nickname" id="nickname" />
        		</td>
        		</tr>
        		<tr>
        		<td />
        			<td id="loginDeksia">
        				<input type="submit" value="Chat Now" class="submit" id="guestLogin" />
        			</td>
        		</tr>
        	
        </table>
    </form>