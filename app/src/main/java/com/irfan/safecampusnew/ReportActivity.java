package com.irfan.safecampusnew;

import android.Manifest;
import android.content.pm.PackageManager;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;

import com.android.volley.Request;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;

import java.util.HashMap;
import java.util.Map;

public class ReportActivity extends AppCompatActivity {

    // UPDATE IP INI JIKA PERLU!
    String url = "http://10.0.2.2/safecampus/report.php";

    EditText etDescription, etOtherType;
    RadioGroup radioGroup;
    RadioButton rbOthers;
    TextView tvLocationInfo;
    Button btnSubmit, btnBackReport;

    FusedLocationProviderClient fusedLocationClient;
    double currentLat = 0.0, currentLng = 0.0;

    // 🟢 1. VARIABLE UNTUK SIMPAN NAMA DARI PAGE SEBELAH
    String receivedUserName = "Anonymous";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_report);

        // 🟢 2. TANGKAP NAMA DARI INTENT MAPS
        String intentName = getIntent().getStringExtra("USER_NAME");
        if (intentName != null) {
            receivedUserName = intentName;
        }

        // Linkkan UI dari XML
        etDescription = findViewById(R.id.etDescription);
        radioGroup = findViewById(R.id.radioGroupType);
        tvLocationInfo = findViewById(R.id.tvLocationInfo);
        btnSubmit = findViewById(R.id.btnSubmitReport);
        etOtherType = findViewById(R.id.etOtherType);
        rbOthers = findViewById(R.id.rbOthers);
        btnBackReport = findViewById(R.id.btnBackReport);

        // Setup Butang Back
        btnBackReport.setOnClickListener(v -> {
            finish();
        });

        // Logik untuk tunjuk/sorok kotak "Others"
        radioGroup.setOnCheckedChangeListener((group, checkedId) -> {
            if (checkedId == R.id.rbOthers) {
                etOtherType.setVisibility(View.VISIBLE);
                etOtherType.requestFocus();
            } else {
                etOtherType.setVisibility(View.GONE);
                etOtherType.setText("");
            }
        });

        // Setup GPS
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this);
        getCurrentLocation();

        btnSubmit.setOnClickListener(v -> submitReport());
    }

    private void getCurrentLocation() {
        if (ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {
            fusedLocationClient.getLastLocation().addOnSuccessListener(this, location -> {
                if (location != null) {
                    currentLat = location.getLatitude();
                    currentLng = location.getLongitude();
                    tvLocationInfo.setText("Location Locked: " + currentLat + ", " + currentLng);
                }
            });
        }
    }

    private void submitReport() {
        String description = etDescription.getText().toString().trim();
        String incidentType = "";

        // Logik Penentuan Jenis Incident
        int selectedId = radioGroup.getCheckedRadioButtonId();

        if (selectedId == R.id.rbOthers) {
            incidentType = etOtherType.getText().toString().trim();
            if (incidentType.isEmpty()) {
                etOtherType.setError("Please specify the type");
                etOtherType.requestFocus();
                return;
            }
        } else {
            RadioButton selectedRadio = findViewById(selectedId);
            if (selectedRadio != null) {
                incidentType = selectedRadio.getText().toString();
            }
        }

        if (description.isEmpty()) {
            etDescription.setError("Please describe the incident");
            return;
        }

        String finalIncidentType = incidentType;

        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    Toast.makeText(this, "Report Sent by " + receivedUserName, Toast.LENGTH_LONG).show();
                    finish();
                },
                error -> Toast.makeText(this, "Error: " + error.getMessage(), Toast.LENGTH_SHORT).show()
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();

                // 🟢 3. GUNA NAMA YANG KITA TANGKAP TADI (BUKAN HARDCODE LAGI)
                params.put("user_name", receivedUserName);

                params.put("incident_type", finalIncidentType);
                params.put("description", description);
                params.put("latitude", String.valueOf(currentLat));
                params.put("longitude", String.valueOf(currentLng));
                return params;
            }
        };

        Volley.newRequestQueue(this).add(request);
    }
}