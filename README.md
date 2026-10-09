# 📦 EmprendoSys - Gestión de Costos Inteligente

**EmprendoSys** es una solución robusta desarrollada para emprendedores que buscan profesionalizar su estructura de costos. El sistema automatiza el cálculo del valor real de producción integrando insumos, mano de obra y empaques.

## ✨ Características Principales
* **Wizard de Producción:** Asistente paso a paso para la creación de recetas y productos.
* **Cálculo de Materia Prima:** Gestión de insumos con cálculo automático por unidad de medida (Kg/ml).
* **Costos de Mano de Obra:** Registro de procesos productivos y cálculo de costos por hora-hombre.
* **Suministros y Empaque:** Control total de materiales indirectos que afectan el precio final.
* **Análisis de Ganancia:** Sugerencia automática de PVP (Precio de Venta al Público) basado en márgenes configurables.

## 🛠️ Stack Tecnológico
* **Framework:** Laravel 12
* **Reactividad:** Livewire 3
* **Estilos:** Tailwind CSS
* **Componentes:** Flux UI (Standard)
* **Base de Datos:** SQLite por defecto; MySQL también está configurado.

## 💻 Instalación local

Necesitas PHP 8.3 o superior con las extensiones de Laravel y SQLite habilitadas, Composer, Node.js y npm, y Git.

```bash
git clone https://github.com/JonathanVincentBa/EmprendoSys-costos.git
cd EmprendoSys-costos
composer setup
composer run dev
```

`composer setup` instala las dependencias de PHP y JavaScript, crea `.env` y la base SQLite, genera la clave de la aplicación, ejecuta las migraciones y compila los recursos. Al iniciar el entorno con `composer run dev`, abre la dirección local que muestra Laravel en la terminal.

Para iniciar sesión por primera vez, registra un usuario desde la pantalla de registro de la aplicación.

## 🚀 Próximas Actualizaciones
- [ ] Punto de Venta (POS) integrado.
- [ ] Facturación Electrónica.
- [ ] Alarmas de Stock Mínimo con notificaciones.

---
Desarrollado con ❤️ para impulsar el crecimiento de los emprendedores.
