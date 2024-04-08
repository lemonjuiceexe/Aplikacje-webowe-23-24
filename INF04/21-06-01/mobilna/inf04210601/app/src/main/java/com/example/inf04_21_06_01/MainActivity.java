package com.example.inf04_21_06_01;

import androidx.appcompat.app.AppCompatActivity;

import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

public class MainActivity extends AppCompatActivity {

    Button submit;
    TextView result;

    EditText email;
    EditText password;
    EditText password2;

     /********************************************************
     * nazwa funkcji: onCreate
     * parametry wejściowe: (Bundle) savedInstanceState - zapisany stan instancji
     * wartość zwracana: -
     * autor: Franciszek Niwicki
     * ****************************************************/
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        submit = findViewById(R.id.submit);
        result = findViewById(R.id.result);
        email = findViewById(R.id.email);
        password = findViewById(R.id.password);
        password2 = findViewById(R.id.password2);

        submit.setOnClickListener(v -> {
            boolean emailValid = stringContains(String.valueOf(email.getText()), '@');
            boolean passwordValid =
                    String.valueOf(password.getText())
                            .equals(String.valueOf(password2.getText()));

            String resultMessage = "";
            resultMessage += emailValid ? "" : "Nieprawidłowy adres e-mail\n";
            resultMessage += passwordValid ? "" : "Hasła się różnią";
            resultMessage = !(emailValid && passwordValid) ?
                    resultMessage : "Witaj " + email.getText();

            result.setText(resultMessage);
        });
    }

     /********************************************************
     * nazwa funkcji: stringContains
     * parametry wejściowe:
     * - (String) text - tekst do przeszukania
     * - (char) character - znak do znalezienia
     * wartość zwracana: (boolean) - prawda jeśli tekst zawiera znak, inaczej fałsz
     * autor: Franciszek Niwicki
     * ****************************************************/
    private boolean stringContains(String text, char character){
        for(int i = 0; i < text.length(); i++){
            if(text.charAt(i) == (character)){
                return true;
            }
        }
        return false;
    }
}