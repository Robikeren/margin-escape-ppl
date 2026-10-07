from flask import Flask, request, jsonify
import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
from datetime import datetime, timedelta

app = Flask(__name__)

@app.route('/predict', methods=['POST'])
def predict():
    data = request.get_json()
    riwayat = data.get('riwayat', [])
    hari_prediksi = data.get('hari_prediksi', 7)

    if len(riwayat) < 10:
        return jsonify({
            'status': 'error',
            'message': 'Data historis belum cukup untuk training model (minimal 10 data)'
        }), 422

    df = pd.DataFrame(riwayat)
    df['tanggal'] = pd.to_datetime(df['tanggal'])
    df = df.sort_values('tanggal').reset_index(drop=True)

    # Feature engineering
    tanggal_awal = df['tanggal'].min()
    df['hari_index'] = (df['tanggal'] - tanggal_awal).dt.days
    df['is_weekend'] = df['tanggal'].dt.dayofweek.isin([4, 5]).astype(int)  # Jumat=4, Sabtu=5

    X = df[['hari_index', 'is_weekend']].values
    y = df['keluar'].values

    model = LinearRegression()
    model.fit(X, y)

    tanggal_terakhir = df['tanggal'].max()
    hari_index_terakhir = df['hari_index'].max()

    prediksi_harian = []
    for i in range(1, hari_prediksi + 1):
        tanggal_proyeksi = tanggal_terakhir + timedelta(days=i)
        hari_idx = hari_index_terakhir + i
        is_weekend = 1 if tanggal_proyeksi.dayofweek in [4, 5] else 0
        pred = model.predict([[hari_idx, is_weekend]])[0]
        pred = max(pred, 0)
        prediksi_harian.append({
            'tanggal': tanggal_proyeksi.strftime('%Y-%m-%d'),
            'prediksi_keluar': round(float(pred), 1)
        })

    total_prediksi = sum(p['prediksi_keluar'] for p in prediksi_harian)

    pred_weekend = model.predict([[hari_index_terakhir + 1, 1]])[0]
    pred_weekend = max(pred_weekend, 0)

    return jsonify({
        'status': 'success',
        'data': {
            'model': 'Linear Regression (scikit-learn)',
            'koefisien': {
                'hari_index': round(float(model.coef_[0]), 4),
                'is_weekend': round(float(model.coef_[1]), 4),
                'intercept': round(float(model.intercept_), 4)
            },
            'prediksi_harian': prediksi_harian,
            'total_prediksi_periode': round(total_prediksi, 1),
            'prediksi_konsumsi_hari_ramai': round(float(pred_weekend), 1)
        }
    })

if __name__ == '__main__':
    app.run(port=5000, debug=True)