#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>

const String ACCESS_TYPE = "entrada"; // Puedes cambiar según tus necesidades
const String DEVICE_NAME = "ESP8266"; // Nombre del dispositivo

/*
In the ESP8266, D3 pin is RST_PIN and
D4 pin is SS_PIN
*/
#define RST_PIN 0  // Cambiar a número de pin real
#define SS_PIN 2   // Cambiar a número de pin real


MFRC522 reader(SS_PIN, RST_PIN);
MFRC522::MIFARE_Key key;

// Credentials to connect to the wifi network
const char *ssid = "CHAFLOQUE_V 2.4 GHz";
const char *password = "JEAN2630";
/*
The ip or server address. If you are on localhost, put your computer's IP (for example http://192.168.1.65)
If the server is online, put the server's domain for example https://parzibyte.me
*/
const String SERVER_ADDRESS = "http://192.168.101.29/asistencia-php";

void setup() {
  Serial.begin(9600);
  delay(1000);  // Esperar un momento después de iniciar Serial
  Serial.println("Iniciando...");
  // Connect to wifi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
  }

  SPI.begin();

  reader.PCD_Init();
  // Just wait some seconds...
  delay(4);
  // Prepare the security key for the read and write functions.
  // Normally it is 0xFFFFFFFFFFFF
  // Note: 6 comes from MF_KEY_SIZE in MFRC522.h
  for (byte i = 0; i < 6; i++) {
    key.keyByte[i] = 0xFF;  //keyByte is defined in the "MIFARE_Key" 'struct' definition in the .h file of the library
  }
}

void loop() {
  // If not connected, we don't need to read anything, that would be unnecessary
  if (WiFi.status() != WL_CONNECTED) {
    return;
  }
  // But, if there is a connection we check if there's a new card to read

  // Reset the loop if no new card present on the sensor/reader. This saves the entire process when idle.
  if (!reader.PICC_IsNewCardPresent()) {
    Serial.println("No hay tarjeta presente");
      delay(1000);
  
    return;
  }

  // Select one of the cards. This returns false if read is not successful; and if that happens, we stop the code
  if (!reader.PICC_ReadCardSerial()) {
    return;
  }

  /*
    At this point we are sure that: there is a card that can be read, and there's a
    stable connection. So we read the id and send it to the server
  */
  String serial = "";
  for (int x = 0; x < reader.uid.size; x++) {
    // If it is less than 10, we add zero
    if (reader.uid.uidByte[x] < 0x10) {
      serial += "0";
    }
    // Transform the byte to hex
    serial += String(reader.uid.uidByte[x], HEX);
    // Add a hypen
    if (x + 1 != reader.uid.size) {
      serial += "-";
    }
  }

  // Transform to uppercase
  serial.toUpperCase();

  // Halt PICC
  reader.PICC_HaltA();
  // Stop encryption on PCD
  reader.PCD_StopCrypto1();

  HTTPClient http;

  // Send the tag id in a GET param
  // Enviar datos al servidor
  const String full_url = SERVER_ADDRESS + "/rfid_register.php?serial=" + serial + "&access=" + ACCESS_TYPE + "&device=" + DEVICE_NAME;
  WiFiClient client;
  http.begin(client, full_url);


  // Enviar la solicitud HTTP
  Serial.print("Enviando solicitud HTTP a: ");
  Serial.println(full_url);
  // Make request
  int httpCode = http.GET();
  if (httpCode > 0) {
    if (httpCode == HTTP_CODE_OK) {

      // const String &payload = http.getString().c_str(); //Get the request response payload
    } else {
    }
  } else {
  }

  Serial.print("Código de respuesta HTTP: ");
  Serial.println(httpCode);

  http.end();  //Close connection
}
