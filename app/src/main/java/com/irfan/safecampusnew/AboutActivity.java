package com.irfan.safecampusnew;

import android.os.Bundle;
import android.widget.Button; // Jangan lupa import Button
import androidx.appcompat.app.AppCompatActivity;

public class AboutActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_about);

        // 🟢 KOD BARU: Setup Butang Back
        Button btnBack = findViewById(R.id.btnBack);
        btnBack.setOnClickListener(v -> {
            finish(); // Tutup page About, automatik balik ke page belakang (Map)
        });

        // (Optional) Kalau nak kekalkan butang back kat Action Bar atas sekali
        if (getSupportActionBar() != null) {
            getSupportActionBar().setTitle("About Us");
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        }
    }

    @Override
    public boolean onSupportNavigateUp() {
        finish();
        return true;
    }
}