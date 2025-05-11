# app/Services/Python/validate_ppn.py

import sys
import pdfplumber
import re
import json

file_path = sys.argv[1]
expected_name = sys.argv[2]

def extract_info(path, expected_name):
    with pdfplumber.open(path) as pdf:
        text = '\n'.join(page.extract_text() or '' for page in pdf.pages)

    name_found = expected_name in text

    match = re.search(r"\d+\s+x\s+(.*?)(\n|Rp|\d{1,3}(\.\d{3})*,\d{2})", text)
    nama_barang = match.group(1).strip() if match else None
    nama_barang_found = bool(nama_barang)

    npwp_matches = re.findall(r"\d{4} \d{4} \d{4} \d{4}", text)
    npwp_found = len(npwp_matches) >= 1

    faktur_found = bool(re.search(r"\d{3}\.\d{3}-\d{2}\.\d{8}", text))

    nominal_matches = re.findall(r"\d{1,3}(\.\d{3})*,\d{2}", text)
    nominal_found = len(nominal_matches) >= 1

    return {
        "nama_penerima_ditemukan": name_found,
        "nama_barang_ditemukan": nama_barang_found,
        "nama_barang": nama_barang if nama_barang_found else None,
        "npwp_ditemukan": npwp_found,
        "faktur_pajak_ditemukan": faktur_found,
        "nominal_ditemukan": nominal_found
    }

result = extract_info(file_path, expected_name)
print(json.dumps(result))
