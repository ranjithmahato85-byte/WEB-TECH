@echo off
title Nepal Online Voting System
echo ==========================================================
echo   Nepal Online Voting System - Local Server
echo ==========================================================
echo Starting PHP server at http://localhost:8000 ...
echo.
echo (For the recommended XAMPP/Apache setup, see XAMPP_SETUP.md)
echo Press Ctrl+C to stop the server.
echo.
php -S localhost:8000 router.php
pause
