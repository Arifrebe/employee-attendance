#include <ArduinoJson.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <LiquidCrystal_I2C.h>

// Konfigurasi WiFi
const char* ssid = "Samsung A22 5G";
const char* password = "sgbg01637";

// Endpoint API tujuan
const char* host = "192.168.202.72";
const char* apiPath = "/employe-attendances/card-attendance";

// Pin RFID & Buzzer
#define SDA_PIN 2      // D4
#define RST_PIN 3      // D3
#define BUZZER_PIN 15  // D8

MFRC522 mfrc522(SDA_PIN, RST_PIN);
LiquidCrystal_I2C lcd(0x27, 16, 2);  // Alamat I2C LCD

void printToLCD(String text) {
  lcd.clear();

  int splitIndex = 16;
  if (text.length() > 16) {
    for (int i = 15; i >= 0; i--) {
      if (text.charAt(i) == ' ') {
        splitIndex = i;
        break;
      }
    }
  }

  String line1 = text.substring(0, splitIndex);
  String line2 = "";

  if (text.length() > splitIndex) {
    line2 = text.substring(splitIndex);
    line2.trim(); // Hapus spasi di awal
  }

  lcd.setCursor(0, 0);
  lcd.print(line1);
  lcd.setCursor(0, 1);
  lcd.print(line2.substring(0, 16)); // Batas maksimal LCD
}

void setup() {
  Serial.begin(115200);

  // LCD
  lcd.begin();
  lcd.backlight();
  lcd.setCursor(0, 0);
  lcd.print("Inisialisasi...");

  // WiFi
  WiFi.hostname("NodeMCU");
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  lcd.clear();
  lcd.print("WiFi Terhubung");
  lcd.setCursor(0, 1);
  lcd.print(WiFi.localIP());
  delay(2000);
  lcd.clear();

  // RFID
  SPI.begin();
  mfrc522.PCD_Init();
  lcd.print("Dekatkan kartu");

  // Buzzer
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);
}

void loop() {
  if (!mfrc522.PICC_IsNewCardPresent() || !mfrc522.PICC_ReadCardSerial())
    return;

  // Ambil UID RFID
  String IDTAG = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    IDTAG += String(mfrc522.uid.uidByte[i], HEX);
  }
  IDTAG.toUpperCase();

  Serial.print("Kartu Terdeteksi: ");
  Serial.println(IDTAG);

  // Kirim ke API
  if (WiFi.status() == WL_CONNECTED) {
    WiFiClient client;
    HTTPClient http;

    String url = String("http://") + host + apiPath;
    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");

    String jsonPayload = "{\"rfid\":\"" + IDTAG + "\"}";
    int httpCode = http.POST(jsonPayload);

    if (httpCode > 0) {
      String response = http.getString();
      Serial.println("Respon: " + response);

      StaticJsonDocument<200> doc;
      DeserializationError error = deserializeJson(doc, response);

      if (!error) {
        const char* message = doc["message"];
        printToLCD(String(message));
      } else {
        printToLCD("Respon error");
      }

      // Buzzer on
      digitalWrite(BUZZER_PIN, HIGH);
      delay(500);
      digitalWrite(BUZZER_PIN, LOW);
    } else {
      printToLCD("Gagal Kirim\n" + http.errorToString(httpCode));

      for (int i = 0; i < 2; i++) {
        digitalWrite(BUZZER_PIN, HIGH);
        delay(200);
        digitalWrite(BUZZER_PIN, LOW);
        delay(200);
      }

      Serial.println("Error kirim: " + http.errorToString(httpCode));
    }

    http.end();
  }

  delay(3000);
  lcd.clear();
  lcd.print("Dekatkan kartu");
}
