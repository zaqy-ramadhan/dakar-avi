# Dokumentasi API v1 Dakar-AVI

Dokumentasi ini menjelaskan penggunaan Application Programming Interface (API) v1 pada sistem **Dakar-AVI**. API ini menyediakan akses data Pegawai (*Users*), Departemen (*Departments*), dan Posisi/Jabatan (*Positions*).

---

## 📌 Ketentuan Umum & Otentikasi

### Base URL
```http
http://<your-domain-or-ip>/api/v1
```

### Authorization Header
Seluruh endpoint API v1 dilindungi menggunakan otentikasi **Bearer API Key**. Setiap request wajib menyertakan header `Authorization`:

```http
Authorization: Bearer <API_KEY>
```

> 💡 **Catatan**: Nilai `<API_KEY>` harus sesuai dengan variabel lingkungan `API_KEY` yang dikonfigurasi pada file `.env` server.

---

## 📑 Ringkasan Endpoint

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | [`/api/v1/users`](#1-get-apiv1users) | Mengambil daftar pegawai |
| `GET` | [`/api/v1/users/{id}`](#2-get-apiv1usersid) | Mengambil detail pegawai berdasarkan NPK |
| `GET` | [`/api/v1/department`](#3-get-apiv1department) | Mengambil daftar departemen |
| `GET` | [`/api/v1/department/{id}`](#4-get-apiv1departmentid) | Mengambil detail departemen berdasarkan ID |
| `GET` | [`/api/v1/position`](#5-get-apiv1position) | Mengambil daftar posisi / jabatan |
| `GET` | [`/api/v1/position/{id}`](#6-get-apiv1positionid) | Mengambil detail posisi / jabatan berdasarkan ID |

---

## 🔒 Handling Otentikasi & Standard Error Responses

Jika request tidak memenuhi kriteria otentikasi atau terjadi kesalahan pada server, API akan mengembalikan respons kesalahan berikut:

### 1. 401 Unauthorized (Header Hilang atau Format Salah)
Dikembalikan jika header `Authorization` tidak disertakan atau tidak menggunakan awalan `Bearer `.
```json
{
  "error": "Unauthorized. API key missing or invalid."
}
```

### 2. 401 Unauthorized (API Key Tidak Valid)
Dikembalikan jika token yang dikirimkan tidak cocok dengan `API_KEY` di server.
```json
{
  "error": "Unauthorized. Invalid API key."
}
```

---

## 🚀 Detail Endpoint

### 1. `GET /api/v1/users`
Mengambil daftar pegawai beserta detail pekerjaan, unit kerja, kontak, dan status kepegawaiannya.

#### Controller Action
[`ApiUsersController@index`](file:///c:/inetpub/wwwroot/dakar/app/Http/Controllers/Api/v1/ApiUsersController.php#L18)

#### Query Parameters
| Parameter | Tipe Data | Required | Deskripsi |
| :--- | :--- | :--- | :--- |
| `search` | `string` | Tidak | Pencarian berdasarkan `npk` atau `fullname` pegawai. |
| `showAll` | `boolean` | Tidak | Jika `true` / `1`, menampilkan semua pegawai (termasuk yang non-aktif/`employment_status = false`). Default: `false`. |
| `filter` | `string` | Tidak | Filter pegawai berdasarkan `user_dakar_role` (misal: `user`, `manager`, dll). |

#### Catatan Filter Internal
- User dengan role admin (`admin`, `admin 2`, `admin 3`, `admin 4`) diabaikan/tidak ditampilkan pada endpoint ini.

#### Response Success (`200 OK`)
```json
{
  "total": 1,
  "data": [
    {
      "id": 10,
      "npk": "12345",
      "fullname": "Budi Santoso",
      "email_avi": "budi.santoso@avi.co.id",
      "email": "budi@example.com",
      "position_id": 5,
      "position": "Software Engineer",
      "section_id": 12,
      "section": "Application Development",
      "department_id": 3,
      "department": "Information Technology",
      "division_id": 2,
      "division": "Digital & Technology",
      "cost_center_id": 4,
      "cost_center": "CC-IT-001",
      "job_type": "Permanent",
      "golongan": "III",
      "sub_golongan": "III/A",
      "group": "Group A",
      "line_id": 1,
      "line": "Line 1",
      "level": "Staff",
      "work_hour": "08:00 - 17:00",
      "job_status": "Karyawan Tetap",
      "job_role": "user",
      "join_date": "2021-06-01",
      "start_date": "2021-06-01",
      "end_date": null,
      "contract": null,
      "gender": "Laki-laki",
      "age": 29,
      "no_telp": "081234567890",
      "employment_status": true
    }
  ],
  "message": "Employees fetched successfully."
}
```

#### Response Error (`500 Internal Server Error`)
```json
{
  "error": "Failed to fetch employees.<detail_exception>"
}
```

---

### 2. `GET /api/v1/users/{id}`
Mengambil detail informasi pegawai spesifik berdasarkan nomor **NPK**.

#### Controller Action
[`ApiUsersController@show`](file:///c:/inetpub/wwwroot/dakar/app/Http/Controllers/Api/v1/ApiUsersController.php#L123)

#### Path Parameters
| Parameter | Tipe Data | Required | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | `string` | **Ya** | Nomor **NPK** pegawai (contoh: `12345`). |

> ⚠️ **Penting**: Nilai `{id}` pada URL endpoint ini diisi dengan nomor **NPK** pegawai, bukan ID numerik primary key database.

#### Response Success (`200 OK`)
```json
{
  "data": {
    "id": 10,
    "npk": "12345",
    "fullname": "Budi Santoso",
    "email": "budi@example.com",
    "email_avi": "budi.santoso@avi.co.id",
    "position": "Software Engineer",
    "section": "Application Development",
    "department": "Information Technology",
    "division": "Digital & Technology",
    "cost_center": "CC-IT-001",
    "job_type": "Permanent",
    "golongan": "III",
    "sub_golongan": "III/A",
    "group": "Group A",
    "line": "Line 1",
    "level": "Staff",
    "work_hour": "08:00 - 17:00",
    "job_status": "Karyawan Tetap",
    "join_date": "2021-06-01",
    "start_date": "2021-06-01",
    "end_date": null,
    "contract": null,
    "no_telp": "081234567890"
  },
  "message": "Employee details fetched successfully."
}
```

#### Response Error (`404 Not Found`)
```json
{
  "error": "User not found.<detail_exception>"
}
```

---

### 3. `GET /api/v1/department`
Mengambil daftar departemen yang aktif (`is_active = true`) beserta divisi dan Department Head (Manager).

#### Controller Action
[`ApiDepartmentController@index`](file:///c:/inetpub/wwwroot/dakar/app/Http/Controllers/Api/v1/ApiDepartmentController.php#L18)

#### Query Parameters
*(Tidak ada query parameter)*

#### Response Success (`200 OK`)
```json
{
  "data": [
    {
      "id": 3,
      "name": "Information Technology",
      "division": "Digital & Technology",
      "manager": "Ahmad Subagyo",
      "manager_id": 2
    }
  ],
  "total": 1,
  "message": "Departments fetched successfully."
}
```

#### Response Error (`500 Internal Server Error`)
```json
{
  "error": "Failed to fetch departments.<detail_exception>"
}
```

---

### 4. `GET /api/v1/department/{id}`
Mengambil detail satu departemen berdasarkan ID departemen.

#### Controller Action
[`ApiDepartmentController@show`](file:///c:/inetpub/wwwroot/dakar/app/Http/Controllers/Api/v1/ApiDepartmentController.php#L69)

#### Path Parameters
| Parameter | Tipe Data | Required | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | `integer` | **Ya** | ID primary key dari departemen. |

#### Response Success (`200 OK`)
```json
{
  "data": {
    "id": 3,
    "name": "Information Technology",
    "division": "Digital & Technology",
    "manager": "Ahmad Subagyo",
    "manager_id": 2
  },
  "message": "Department details fetched successfully."
}
```

#### Response Error (`404 Not Found`)
```json
{
  "error": "Department not found.<detail_exception>"
}
```

---

### 5. `GET /api/v1/position`
Mengambil daftar posisi / jabatan yang aktif (`is_active = true`) beserta departemen penyertanya dan daftar pegawai aktif yang menduduki posisi tersebut.

#### Controller Action
[`ApiPositionController@index`](file:///c:/inetpub/wwwroot/dakar/app/Http/Controllers/Api/v1/ApiPositionController.php#L18)

#### Query Parameters
*(Tidak ada query parameter)*

#### Response Success (`200 OK`)
```json
{
  "data": [
    {
      "id": 5,
      "name": "Software Engineer",
      "department": "Information Technology",
      "employee": [
        {
          "id": 10,
          "fullname": "Budi Santoso",
          "email": "budi@example.com"
        }
      ]
    }
  ],
  "total": 1,
  "message": "Positions fetched successfully."
}
```

#### Response Error (`500 Internal Server Error`)
```json
{
  "error": "Failed to fetch positions.<detail_exception>"
}
```

---

### 6. `GET /api/v1/position/{id}`
Mengambil detail posisi / jabatan berdasarkan ID posisi.

#### Controller Action
[`ApiPositionController@show`](file:///c:/inetpub/wwwroot/dakar/app/Http/Controllers/Api/v1/ApiPositionController.php#L74)

#### Path Parameters
| Parameter | Tipe Data | Required | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | `integer` | **Ya** | ID primary key dari posisi/jabatan. |

#### Response Success (`200 OK`)
```json
{
  "data": {
    "id": 5,
    "name": "Software Engineer",
    "department": "Information Technology",
    "employee": [
      {
        "id": 10,
        "fullname": "Budi Santoso",
        "email": "budi@example.com"
      }
    ]
  },
  "message": "Position details fetched successfully."
}
```

#### Response Error (`404 Not Found`)
```json
{
  "error": "Position not found.<detail_exception>"
}
```

---

## 💻 Contoh Penggunaan Client

### cURL
```bash
# Get all users (Active only)
curl -X GET "http://localhost/api/v1/users" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"

# Search user by NPK or Fullname
curl -X GET "http://localhost/api/v1/users?search=12345&showAll=true" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"

# Get user by NPK
curl -X GET "http://localhost/api/v1/users/12345" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"
```

### JavaScript (Fetch API)
```javascript
const API_KEY = 'YOUR_API_KEY';

async function fetchUsers() {
  const response = await fetch('/api/v1/users', {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${API_KEY}`,
      'Accept': 'application/json'
    }
  });
  
  const result = await response.json();
  console.log(result);
}
```

### PHP (Guzzle HTTP)
```php
use GuzzleHttp\Client;

$client = new Client();
$response = $client->request('GET', 'http://localhost/api/v1/users', [
    'headers' => [
        'Authorization' => 'Bearer YOUR_API_KEY',
        'Accept'        => 'application/json',
    ],
]);

$data = json_decode($response->getBody(), true);
```
