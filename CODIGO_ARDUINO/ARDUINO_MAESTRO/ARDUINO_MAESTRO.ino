#include <Wire.h>
#include <LiquidCrystal_I2C.h>

LiquidCrystal_I2C lcd(0x27, 16, 2); // Dirección I2C 0x27, pantalla 16x2

void setup() {
  Serial.begin(9600);
  lcd.begin(16, 2); // Inicializar el LCD
  lcd.backlight(); // Encender la retroiluminación del LCD
}

void loop() {
  if (Serial.available() > 0) {
    String data = Serial.readStringUntil('\n'); // Lee los datos hasta encontrar un salto de línea
    // Procesa los datos recibidos
    Serial.println("Datos recibidos: " + data);

    // Buscar la posición del primer carácter '='
    int posInicio = data.indexOf('=');

    // Buscar la posición del primer carácter '&'
    int posFin = data.indexOf('&');

    // Verificar si se encontraron ambas posiciones
    if (posInicio != -1 && posFin != -1 && posInicio < posFin) {
      // Extraer la parte entre '=' y '&'
      String identificacion = data.substring(posInicio + 1, posFin);

      Serial.println("Identificación de la tarjeta: " + identificacion);

      // Llenar con espacios en blanco si la longitud es menor a 16
      while (identificacion.length() < 16) {
        identificacion += ' ';
      }

      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("Tarjeta RFID:");
      lcd.setCursor(0, 1);
      lcd.print(identificacion);
      delay(3000); // Aumenté el tiempo de visualización a 3 segundos
      lcd.clear();
    } else {
      // Si no se detecta una tarjeta, muestra un mensaje diferente
      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print("Bienvenido");
      lcd.setCursor(0, 1);
      lcd.print("Esperando RFID");
      delay(1000); // Puedes ajustar este retardo según sea necesario
      lcd.clear();
    }
  }
}