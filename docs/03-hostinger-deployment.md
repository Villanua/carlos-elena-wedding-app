# Despliegue en Hostinger (Sitio Web PHP/HTML)

## Estructura del proyecto

```
carlos-elena-wedding-app/
├── index.html          ← Página principal
├── css/
├── js/
├── img/
├── .gitignore
├── README.md
└── docs/               ← Documentación
```

---

## Despliegue

### Opción A: Subida manual por File Manager

1. Entra al **hPanel** de Hostinger → **Administrador de archivos**.
2. Navega a `public_html/`.
3. Sube el archivo `index.html` (y cualquier asset adicional) directamente ahí.
4. Listo — tu dominio servirá `index.html` automáticamente.

### Opción B: Git + Auto Deploy

1. Sube tu código a un repo en GitHub.
2. En hPanel → **Git** → conecta el repositorio.
3. Configura que el **Branch** sea `main` y el **destino** sea `public_html`.
4. Cada push a `main` actualizará el sitio automáticamente.

---

## Notas

- No se necesita Node.js, Express, ni `package.json`. Apache/LiteSpeed sirve los archivos estáticos directamente.
- El archivo debe llamarse `index.html` para que sea la página por defecto.
- Si necesitas redirecciones o URLs personalizadas, usa un archivo `.htaccess`.
