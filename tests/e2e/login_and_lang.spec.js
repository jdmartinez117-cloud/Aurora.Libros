// tests/e2e/login_and_lang.spec.js
// Prueba End-to-End (E2E) con Playwright para Aurora.Libros
// Ejecutar con: npx playwright test

const { test, expect } = require('@playwright/test');

test.describe('Aurora.Libros - Flujos E2E de Usuario', () => {

    test.beforeEach(async ({ page }) => {
        // Carga la página principal del proyecto
        await page.goto('http://localhost/Aurora.Libros/index.php');
    });

    test('1. Conmutador de Idiomas traduce dinámicamente la interfaz', async ({ page }) => {
        // Verifica que el botón flotante exista
        const switchLang = page.locator('#lang-switch');
        await expect(switchLang).toBeVisible();

        // Da clic en inglés 'EN'
        await page.click('#lang-switch button[data-l="en"]');

        // Comprueba que los textos cambien al inglés (Entrar -> Sign In)
        await expect(page.locator('.formulario__login h2', { hasText: 'Sign In' })).toBeVisible();
        await expect(page.locator('#btn__registrarse', { hasText: 'Sign Up' })).toBeVisible();

        // Regresa a español 'ES'
        await page.click('#lang-switch button[data-l="es"]');
        await expect(page.locator('.formulario__login h2', { hasText: 'Entrar' })).toBeVisible();
    });

    test('2. Checklist de Contraseña reacciona en tiempo real a los requisitos', async ({ page }) => {
        // Clic en registrarse para mostrar el formulario
        await page.click('#btn__registrarse');

        const passInput = page.locator('.formulario__register input[name="password"]');
        const chipMayus = page.locator('.formulario__register .pw-chip[data-regla="mayus"]');
        const chipNumero = page.locator('.formulario__register .pw-chip[data-regla="numero"]');

        // Escribe solo minúsculas
        await passInput.fill('aurora');
        await expect(chipMayus).toHaveClass(/falta/);

        // Agrega mayúscula y número
        await passInput.fill('Aurora2026');
        await expect(chipMayus).toHaveClass(/ok/);
        await expect(chipNumero).toHaveClass(/ok/);

        // Resumen debe indicar lista
        await expect(page.locator('.formulario__register .pw-resumen')).toContainText('¡Contraseña lista!');
    });

    test('3. Modal de Acceso Administrador abre y cierra con Escape', async ({ page }) => {
        const modal = page.locator('#modalAdmin');
        await expect(modal).not.toBeVisible();

        // Abrir modal
        await page.click('#btn-open-admin');
        await expect(modal).toBeVisible();

        // Presionar ESC para cerrar
        await page.keyboard.press('Escape');
        await expect(modal).not.toBeVisible();
    });
});
