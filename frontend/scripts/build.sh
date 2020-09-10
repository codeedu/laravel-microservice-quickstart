
rm -rf /var/www/backend/public/admin-frontend
cp -R /var/www/frontend/build /var/www/backend/public/admin-frontend
mkdir -p /var/www/backend/resources/views/admin-frontend
mv /var/www/backend/public/admin-frontend/index.html /var/www/backend/resources/views/admin-frontend/index.html