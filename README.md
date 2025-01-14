<p align="center">
    <img src="public/tanma.png" width="200" alt="Tanma Logo">
</p>

<h1 align="center">TANMA - Report System</h1>

<p align="center">
     •<a href="#about">Tentang</a> •
    <a href="#features">Fitur</a> •
    <a href="#installation">Cara Install</a> •
    <!-- <a href="#usage">Usage</a> • -->
</p>

## About

TANMA adalah project kampus untuk tugas besar matakuliah pengembangan perangkat lunak. yang berisikan informasi report harian kerja serta alat-alat menunjang kerja.

## Features
✨ **Core Features**
- User & Role Management
- Report Daily
- Meetings Topic
- Tools (MergePDF, SelectPDF, SplitBill)

🔒 **Security**
- Role-Based Access Control
- Secure Authentication
- Activity Logging

📱 **Technology**
- Laravel 11
- MySQL Database
- Mobile Responsive Design

## Installation

1. Clone repository:
```bash
git clone https://github.com/fravadearu/tanma.git
cd tanma
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
composer run dev
