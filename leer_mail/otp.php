<?php
require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/sftp.php');
require_once(getcwd() . '/cloudflare.php');

class OTP
{

    private $crm = "";
    private $oportunidad = 0;

    public function getCrm()
    {
        return $this->crm;
    }
    public function getOportunidad()
    {
        return $this->oportunidad;
    }
    private function setCrm($value): void
    {
        $this->crm = $value;
    }
    private function setOportunidad($value): void
    {
        $this->oportunidad = $value;
    }

    public function obtenerCodigo($codigo): bool
    {
        $b = false;
        $b = $this->formatearCodigo($codigo);
        return $b;
    }

    public function verificarSubida($archivo): bool
    {
        $b = file_exists("/var/www/html/maat/webroot/plantillas/contratos_firmados/" . $archivo);
        // $sftp = new SFTP();
        // $stpConnection = ssh2_connect($sftp->getServidor(), $sftp->getPuerto());
        // if (ssh2_auth_password($stpConnection, $sftp->getUsuario(), $sftp->getPwd())) {
        //     $b = file_exists('ssh2.sftp://' . $stpConnection . $sftp->getRuta() . $archivo);
        //     ssh2_disconnect($stpConnection);
        // }
        return $b;
    }

    public function subirContrato($archivo, $crmId, $oportunidadId): bool
    {
        $b = copy(getcwd() . "/" . $archivo, "/var/www/html/maat/webroot/plantillas/contratos_firmados/" . $archivo);
        $cloudflare = new Cloudflare();
        $b = $cloudflare->uploadFile(getcwd() . "/" . $archivo);

        // $sftp = new SFTP();
        // $stpConnection = ssh2_connect($sftp->getServidor(), $sftp->getPuerto());
        // if (ssh2_auth_password($stpConnection, $sftp->getUsuario(), $sftp->getPwd())) {
        //     $archivo1 = getcwd() . "/" . $archivo;
        //     try {
        //         $b = ssh2_scp_send($stpConnection, $archivo1, $sftp->getRuta() . $archivo);
        //     } catch (Exception $e) {
        //         $b = false;
        //     }
        //     ssh2_disconnect($stpConnection);
        // }
        return $b;
    }

    public function actualizarEstado($crmId, $oportunidadId, $archivo): bool
    {
        $b = false;

        $con = new Conexion();
        $con->conectar();

        $r = $con->query("call otp_actualizar_estado("
            . $crmId . ","
            . $oportunidadId . ");");

        $retorno = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : "0";
        $b = ($retorno == "1") ? true :  false;

        if ($b) {
            $con->next_result();
            $r = $con->query("call oportunidad_contrato_guardar("
                . $crmId . ","
                . $oportunidadId . ",'"
                . $archivo . "')");
            $b = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : "0";
        }
        $con = null;
        return $b;
    }

    private function formatearCodigo($codigo): bool
    {
        $b = false;
        $arrCodigo = explode("--", $codigo);

        try {
            $s = $arrCodigo[2];
            $posicionContrato = strpos($s, "C");

            if ($posicionContrato !== false) {
                $this->setCrm(str_replace("D", "", substr($s, 0, $posicionContrato)));
                $this->setOportunidad(substr($s, $posicionContrato + 1, strlen($codigo)));

                $b = (is_numeric($this->getCrm()) && is_numeric($this->getOportunidad())) ? true : false;
            }
        } catch (Exception $e) {
            $b = false;
        }
        return $b;
    }
}
