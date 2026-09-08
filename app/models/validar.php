 <?php 

class ValidarModel {

	private $patron;
	private $cadena;

	public function Validar($validar, $cadena) {

		switch ($validar) {

			case 'Letras':

				$this->patron = "/^[A-Za-z\s\á\Á\é\É\í\Í\ó\Ó\ú\Ú\ñ\Ñ ]+$/";
				$this->cadena = $cadena;
				return $this->ejecutarValidacion();
			break;

			case 'LetrasPunto':

				$this->patron = "/^[A-Za-z\s\á\Á\é\É\í\Í\ó\Ó\ú\Ú\ñ\Ñ .,]+$/";
				$this->cadena = $cadena;
				return $this->ejecutarValidacion();
			break;

			case 'Numeros':
				
				$this->patron = "/^[0-9]+$/";
				$this->cadena = $cadena;
				return $this->ejecutarValidacion();
			break;

			case 'Moneda':
				
				$this->patron = "/^[0-9 .,]+$/";
				$this->cadena = $cadena;
				return $this->ejecutarValidacion();
			break;

			case 'LetrasNumeros':
				
				$this->patron = "/^[A-Za-z0-9 .,\/_-]+$/";
				$this->cadena = $cadena;
				return $this->ejecutarValidacion();
			break;

			case 'Tlf':
				
				$this->patron = "/^[0-9 +()-]+$/";
				$this->cadena = $cadena;
				return $this->ejecutarValidacion();
			break;

			case 'Fecha':
				
				$this->patron = "/^[0-9 \/-]+$/";
				$this->cadena = $cadena;
				return $this->ejecutarValidacion();
			break;

			case 'Correo':
				
				$this->patron = "/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}\b/";
				// /^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/
				$this->cadena = $cadena;
				return $this->ejecutarValidacion();
			break;
			case 'Caracteres':

				$this->patron = "/^[[:ascii:]]+$/";
				$this->cadena = $cadena;
				return $this->ejecutarValidacion();
			break;
			case 'Password':

				$this->patron = "/^(?=.*\d)(?=.*[\u0021-\u002b\u003c-\u0040])(?=.*[A-Z])(?=.*[a-z])\S{8,16}$/"; //La contraseña debe tener al entre 8 y 16 caracteres, al menos un dígito, al menos una minúscula, al menos una mayúscula y al menos un caracter no alfanumérico
				$this->cadena = $cadena;
				return $this->ejecutarValidacion();
			break;
		}
	}

	public function ejecutarValidacion() {

		if ( preg_match($this->patron, $this->cadena) ) {

			return true;
		} else {

			return false;
		}
	}
}

//av. la cruz, casa 34/33
?>

