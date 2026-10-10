# EmprendoSys

Sistema multiempresa para costos de producción, inventario, punto de venta y facturación electrónica de Ecuador.

## Funciones

- Recetas por lote con materias primas, procesos, mano de obra, empaques y gastos indirectos.
- Cálculo del costo unitario y sugerencia de precio de venta según un recargo configurable.
- Punto de venta con control de stock, desglose del IVA del 15% y formas de pago del SRI.
- Emisión de facturas electrónicas al SRI en ambiente de pruebas o producción.
- Envío al cliente del XML firmado y una representación PDF de la factura con identidad y datos de la empresa.
- Reintento de emisión al SRI para comprobantes fallidos y reenvío independiente del correo de facturas autorizadas.
- Catálogos, clientes, usuarios y datos operativos aislados por empresa.

## Requisitos

PHP 8.3 o superior, Composer, Node.js/npm y las extensiones PHP requeridas por Laravel, incluidas SOAP, cURL y OpenSSL para la integración con el SRI.

## Instalación local

```bash
git clone https://github.com/JonathanVincentBa/EmprendoSys-costos.git
cd EmprendoSys-costos
composer setup
composer run dev
```

`composer setup` instala dependencias, prepara `.env`, genera la clave de la aplicación, ejecuta las migraciones y compila los recursos. Registra un usuario para iniciar sesión.

Antes de emitir facturas, configura en el perfil de la empresa los datos tributarios y la firma electrónica `.p12`; para enviar comprobantes por correo, configura también el servidor SMTP de la empresa. Verifica primero el flujo en ambiente de pruebas antes de seleccionar producción.

## Pruebas

```bash
php artisan test
```
