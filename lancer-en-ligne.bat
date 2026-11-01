@echo off
title Hebergement en ligne - Media Spotlight
echo ===================================================================
echo     HEBERGEMENT PUBLIC MEDIA SPOTLIGHT DEPUIS CET ORDINATEUR
echo ===================================================================
echo.
echo Assurez-vous que les serveurs MAMP sont bien demarres (Apache).
echo Creation du tunnel public securise Cloudflare...
echo.
echo Le lien public HTTPS apparaitra ci-dessous :
echo ===================================================================
echo.
cloudflared tunnel --url http://localhost:80
pause
