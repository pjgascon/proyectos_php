<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);


require_once(getcwd() . '/vendor/Exception.php');
require_once(getcwd() . '/vendor/PHPMailer.php');
require_once(getcwd() . '/vendor/SMTP.php');
require_once(getcwd() . '/vendor/conexion.php');

$procesarCorreo = false;
$c = new Conexion();
$c->conectar();

$r = $c->query("call temporal.obtener_mail_gestoria();");
if ($r->num_rows > 0) {
  $r = $r->fetch_all(MYSQLI_ASSOC);
  $procesarCorreo = true;
  $c->next_result();
} else {
  $c->close();
  echo "Nada que hacer";
  return;
}

if ($procesarCorreo) {
  for ($i = 0; $i < count($r); $i++) {
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {

      $mail->IsSMTP();

      $mail->CharSet = "UTF-8";
      $mail->Host = "smtp.buzondecorreo.com";
      $mail->SMTPDebug = 0;
      $mail->Port = 587; //465 or 587

      $mail->SMTPSecure = 'tls';
      $mail->SMTPAuth = true;
      $mail->IsHTML(true);

      //Authentication
      $mail->Username = "info@liberi.es";
      $mail->Password = "Coral18262202";

      //Set Params
      $mail->setFrom("info@liberi.es", 'Prueba gratis Fichacloud: fichaje digital para tus clientes');
      $mail->AddAddress($r[$i]["mail"]);
      $mail->Subject = "Prueba gratis Fichacloud: fichaje digital para tus clientes";
      $mail->Body = "<div style='background-color:#f0f0f0;padding:0;margin:0;'>
  <table align='center' width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;margin:auto;'>
    <tr>
      <td style='padding:30px 20px;text-align:center;'>
        <h1 style='font-family:Arial,sans-serif;font-size:28px;color:#000;margin:0 0 10px;'>Optimice la gestión laboral de sus clientes con FICHACLOUD</h1>
        <p style='font-family:Arial,sans-serif;font-size:17px;line-height:1.5;color:#4a90e2;margin:0;'>
          <strong>FICHACLOUD</strong> es una herramienta diseñada para que asesorías y gestorías puedan ofrecer a sus clientes una solución avanzada para gestionar horarios, asistencias, vacaciones y ausencias. 
          Pruebe nuestra demo gratuita sin compromiso.
        </p>
      </td>
    </tr>

    <tr><td style='border-top:1px solid #ccc;'></td></tr>

    <!-- Bloques de características -->
    <tr>
      <td style='padding:20px;'>
        <table width='100%' cellpadding='0' cellspacing='0'>
          <tr>
            <td width='35%' align='center'>
              <img src='https://waspapp.mx-router-iv.com/data/2fc60995e709ee09d70aa399a78fcb591e5e2bfa/media_files/1/original/Captura_de_pantalla_2025-10-05_203933.png' width='160' style='display:block;border:0;'>
            </td>
            <td width='65%' style='padding-left:10px;'>
              <h3 style='font-family:Arial,sans-serif;font-size:20px;color:#000;margin:0;'>Fichaje con un solo clic</h3>
              <p style='font-family:Arial,sans-serif;font-size:15px;line-height:1.4;margin:5px 0;color:#333;'>
                Registra entradas, salidas y pausas fácilmente. Solicita ediciones o descansos con un clic.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td style='padding:20px;'>
        <table width='100%' cellpadding='0' cellspacing='0'>
          <tr>
            <td width='35%' align='center'>
              <img src='https://waspapp.mx-router-iv.com/data/2fc60995e709ee09d70aa399a78fcb591e5e2bfa/media_files/2/original/Captura_de_pantalla_2025-10-05_204920.png' width='160' style='display:block;border:0;'>
            </td>
            <td width='65%' style='padding-left:10px;'>
              <h3 style='font-family:Arial,sans-serif;font-size:20px;color:#000;margin:0;'>Gestión de vacaciones y ausencias</h3>
              <p style='font-family:Arial,sans-serif;font-size:15px;line-height:1.4;margin:5px 0;color:#333;'>
                Los empleados pueden solicitar vacaciones directamente. Aprueba o gestiona ausencias al instante.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td style='padding:20px;'>
        <table width='100%' cellpadding='0' cellspacing='0'>
          <tr>
            <td width='35%' align='center'>
              <img src='https://waspapp.mx-router-iv.com/data/2fc60995e709ee09d70aa399a78fcb591e5e2bfa/media_files/3/original/Captura_de_pantalla_2025-10-05_205705.png' width='160' style='display:block;border:0;'>
            </td>
            <td width='65%' style='padding-left:10px;'>
              <h3 style='font-family:Arial,sans-serif;font-size:20px;color:#000;margin:0;'>Horarios y turnos personalizados</h3>
              <p style='font-family:Arial,sans-serif;font-size:15px;line-height:1.4;margin:5px 0;color:#333;'>
                Crea horarios únicos para cada empleado o departamento y envía notificaciones automáticas.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td style='padding:20px;'>
        <table width='100%' cellpadding='0' cellspacing='0'>
          <tr>
            <td width='35%' align='center'>
              <img src='https://waspapp.mx-router-iv.com/data/2fc60995e709ee09d70aa399a78fcb591e5e2bfa/media_files/4/original/Captura_de_pantalla_2025-10-05_210517.png' width='160' style='display:block;border:0;'>
            </td>
            <td width='65%' style='padding-left:10px;'>
              <h3 style='font-family:Arial,sans-serif;font-size:20px;color:#000;margin:0;'>Informes y análisis</h3>
              <p style='font-family:Arial,sans-serif;font-size:15px;line-height:1.4;margin:5px 0;color:#333;'>
                Genera gráficos de horas trabajadas y descarga informes en Excel o PDF.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr><td style='border-top:1px solid #ccc;'></td></tr>

    <!-- CTA -->
    <tr>
      <td style='padding:30px 20px;text-align:center;'>
        <h2 style='font-family:Arial,sans-serif;color:#4a90e2;font-size:26px;margin:0 0 20px;'>Sin permanencia</h2>
        <p style='font-family:Arial,sans-serif;font-size:18px;color:#000;margin:0 0 25px;'>
          Paga solo por las licencias en uso — Desde <strong>0,80€/mes</strong> por licencia.<br>
          Incluye geolocalización del fichaje y autofichajes de salida.
        </p>
        <a href='https://waspapp.mx-router-iv.com/c/z6p2/pgi9w9ox/wuwxic6cdpm' 
           style='background:#d0021b;color:#fff;text-decoration:none;padding:15px 35px;border-radius:4px;font-family:Arial,sans-serif;font-size:17px;display:inline-block;'>
          SOLICITAR DEMO GRATUITA
        </a>
      </td>
    </tr>

    <tr>
      <td style='text-align:center;padding:20px;font-family:Arial,sans-serif;font-size:15px;color:#000;'>
        Si quieres ampliar la información, contáctanos en 
        <a href='tel:722400583' style='color:#4a90e2;text-decoration:none;'>722 400 583</a> o 
        <a href='mailto:info@liberi.es' style='color:#4a90e2;text-decoration:none;'>info@liberi.es</a>
      </td>
    </tr>
  </table>
</div>";


      if (!$mail->Send()) {
        $c->query("call temporal.actualizar_mail_gestoria(" . $r[$i]['id'] . ", -1);");
        echo "Mailer Error: " . $mail->ErrorInfo;
      } else {
        $c->query("call temporal.actualizar_mail_gestoria(" . $r[$i]['id'] . ", 1);");
        echo "Correo " . $i . " enviado info@liberi.es\n\r";
      }
    } catch (Exception $e) {
      $c->query("call temporal.actualizar_mail_gestoria(" . $r[$i]['id'] . ", -1);");
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    usleep(2000000);
  }
  $c->close();
}
