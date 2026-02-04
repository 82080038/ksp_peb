#!/bin/bash

echo "🔧 MEMPERBAIKI SEMUA LINK DI APLIKASI..."
echo ""

# Perbaiki login.php
echo "📝 Perbaiki login.php..."
sed -i 's|href="login.php"|href="/ksp_peb/login.php"|g' /var/www/html/ksp_peb/login.php > /dev/null && \
echo "✅ Login.php diperbaiki!"

# Perbaiki register.php
echo "📝 Perbaiki register.php..."
sed -i 's|href="register.php"|href="/ksp_peb/register.php"|g' /var/www/html/ksp_peb/register.php > /dev/null && \
echo "✅ Register.php diperbaiki!"

# Perbaiki register_cooperative.php
echo "📝 Perbaiki register_cooperative.php..."
sed -i 's|href="register_cooperative.php"|href="/ksp_peb/register_cooperative.php"|g' /var/www/html/ksp_peb/register_cooperative.php > /dev/null && \
echo "✅ Register_cooperative.php diperbaiki!"

# Perbaiki dashboard.php
echo "📝 Perbaiki dashboard.php..."
echo "📝 Perbaiki logout function..."
sed -i 's|window.location.href = \"/ksp_peb/login.php\"|window.location.href = \"/ksp_peb/login.php\"|g' /var/www/html/ksp_peb/dashboard.php > /dev/null && \
echo "✅ Dashboard.php diperbaiki!"

# Perbaiki index.php
echo "📝 Perbaiki index.php..."
echo "📝 Perbaiki redirect di index.php..."
sed -i 's|window.location.href = \"dashboard.php\"|window.location.href = \"/ksp_peb/dashboard.php\"|g' /var/www/html/ksp_peb/index.php > /dev/null && \
echo "✅ Index.php diperbaiki!"

# Perbaiki maintenance.php
echo "📝 Perbaiki maintenance.php..."
sed -i 's|window.location.href = \"login.php\"|window.location.href = \"/ksp_peb/login.php\"|g' /var/www/html/ksp_peb/maintenance.php > /dev/null && \
echo "✅ Maintenance.php diperbaiki!"

echo ""
echo "✅ Semua link telah diperbaiki!"
echo "🚀 Status: SELESAI!"
