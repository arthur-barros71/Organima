# Organima
A web platform for animation continuity review, developed as a TCC (final course project) at Etec Dra. Ruth Cardoso.

------
## About / Sobre:
**EN:** Organima helps animation teams track and communicate continuity mistakes throughout production. Users can divide their project into scenes, use the built-in script editor, and communicate with the team in real time through a commentary system with instant notifications. Developed in 2024–2025 as a TCC project at Etec Dra. Ruth Cardoso.

**PT-BR:** A Organima auxilia equipes de animação a identificar e comunicar erros de continuidade durante a produção. Os usuários podem dividir o projeto em cenas, utilizar o editor de roteiro integrado e se comunicar em tempo real por meio de um sistema de comentários com notificações instantâneas. Desenvolvida em 2024–2025 como projeto de TCC na Etec Dra. Ruth Cardoso.

------
## Features:
- Account creation and editing
- Project creation and editing
- Video/frame upload
- Built-in video player and editor (frame based)
- Scene organization and management
- Built-in script editor
- Real-time team commentary system powered by Pusher
- Continuity mistake flagging and tracking

------
## Tech Stack:
Backend: Laravel, PHP
Database: MySQL
Frontend: JavaScript, HTML5, CSS
Real-time: Pusher

------
## Requirements.
This project requires a local environment to run. There is no hosted version available.
### What you need to run:
PHP 8+
Composer
MySQL
Node.js / NPM

## Setup

```bash
git clone https://github.com/arthur-barros71/organima.git
cd organima
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure your `.env` file with your database credentials and Pusher keys, then run:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

**Additional configuration:**
- Download FFmpeg at https://ffmpeg.org/download.html and add the binary paths to `.env` to use the video/image import/export feature.
- Create a Pusher account at https://pusher.com and configure the credentials in `.env` to use the real-time commentary feature.

------
## Authors:
- **Arthur Barros** — backend, frontend, design
- **Kauan Carvalho** — frontend, documentation
- **Matheus Trindade** — backend, documentation
- **Nicolas Daniel** — team management, documentation
- **Vitória das Neves** — design, animation