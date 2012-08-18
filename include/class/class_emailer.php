<?php
/*
--------------------------------------------------------------------------------
     File:  class_emailer.php

    Class:  EMAIL PROCESS
   Author:  Quoc Viet [aFeLiOn] (Modify From Punbb)
    Begin:  2006-01-16

   Syntax:  new emailer();
            ->mail_send(To, Subject, Message, From = '')
  Require:
    + Object:   [none]
	 + Variable: $eps_config; $eps_lang;
    + Function: [none]

  Comment:
--------------------------------------------------------------------------------
*/

class emailer
{
	function mail_from()
	{
		global $eps_config, $eps_lang;
		return '"'.str_replace('"', '', $eps_config['title'].' '.$eps_lang['Mailer']).'" <'.$eps_config['webmaster_email'].'>';
	}

	function mail_header($from)
	{
		return 'From: '.$from."\r\n".'Date: '.date('r')."\r\n".'MIME-Version: 1.0'."\r\n".'Content-type: text/plain; charset=UTF-8'."\r\n".'X-Mailer: EPS-Mailer';
	}

	function mail_clean($mail)
	{
		return trim(preg_replace('#[\r\n]+#s', '', $mail));
	}

	function mail_linebreak($mail)
	{
		return preg_replace('#(?<!\r)\n#si', "\r\n", $mail);
	}

	function mail_send($to, $subject, $message, $from = '')
	{
		global $eps_config, $eps_lang;

		// Default sender
		if (!$from)
			$from = $this->mail_from();

		// Clean
		$to = $this->mail_clean($to);
		$subject = $this->mail_clean($subject);
		$from = $this->mail_clean($from);

		$header = $this->mail_header($from);

		// All CRLF
		$message = $this->mail_linebreak($message);

		if ($eps_config['smtp_host'] != '')
		{
			$this->smtp_mail($to, $subject, $message, $header);
			return true;
		}
		else
		{
			if (@mail($to, $subject, $message, $header))
				return true;
			else
				return false;
		}
	}

	function server_parse($socket, $expected_response)
	{
		$server_response = '';
		while (substr($server_response, 3, 1) != ' ')
		{
			if (!($server_response = fgets($socket, 256)))
				error('Couldn\'t get mail server response codes. Please contact the forum administrator.', __FILE__, __LINE__);
		}
	
		if (!(substr($server_response, 0, 3) == $expected_response))
			error('Unable to send e-mail. Please contact the forum administrator with the following error message reported by the SMTP server: "'.$server_response.'"', __FILE__, __LINE__);
	}

	function smtp_mail($to, $subject, $message, $headers = '')
	{
		global $eps_config;

		$recipients = explode(',', $to);

		// Custom port?
		if (strpos($eps_config['smtp_host'], ':') !== false)
			list($smtp_host, $smtp_port) = explode(':', $eps_config['smtp_host']);
		else
		{
			$smtp_host = $eps_config['smtp_host'];
			$smtp_port = 25;
		}

		if (!($socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 15)))
			error('Could not connect to smtp host "'.$eps_config['smtp_host'].'" ('.$errno.') ('.$errstr.')', __FILE__, __LINE__);

		$this->server_parse($socket, '220');

		if ($eps_config['smtp_user'] != '' && $eps_config['smtp_pass'] != '')
		{
			fwrite($socket, 'EHLO '.$smtp_host."\r\n");
			$this->server_parse($socket, '250');

			fwrite($socket, 'AUTH LOGIN'."\r\n");
			$this->server_parse($socket, '334');

			fwrite($socket, base64_encode($eps_config['smtp_user'])."\r\n");
			$this->server_parse($socket, '334');

			fwrite($socket, base64_encode($eps_config['smtp_pass'])."\r\n");
			$this->server_parse($socket, '235');
		}
		else
		{
			fwrite($socket, 'HELO '.$smtp_host."\r\n");
			$this->server_parse($socket, '250');
		}

		fwrite($socket, 'MAIL FROM: <'.$eps_config['webmaster_email'].'>'."\r\n");
		$this->server_parse($socket, '250');

		$to_header = 'To: ';
	
		@reset($recipients);
		while (list(, $email) = @each($recipients))
		{
			fwrite($socket, 'RCPT TO: <'.$email.'>'."\r\n");
			$this->server_parse($socket, '250');

			$to_header .= '<'.$email.'>, ';
		}

		fwrite($socket, 'DATA'."\r\n");
		$this->server_parse($socket, '354');

		fwrite($socket, 'Subject: '.$subject."\r\n".$to_header."\r\n".$headers."\r\n\r\n".$message."\r\n");

		fwrite($socket, '.'."\r\n");
		$this->server_parse($socket, '250');

		fwrite($socket, 'QUIT'."\r\n");
		fclose($socket);

		return true;
	}
}
?>
