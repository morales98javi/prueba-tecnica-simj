<script setup>
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import brandLogo from "../../img/LogoSIMJ.png";

const page = usePage();
const sidebarCollapsed = ref(false);

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};
</script>

<template>
    <div
        class="app-shell"
        :class="{ 'app-shell--collapsed': sidebarCollapsed }"
    >
        <header class="app-header">
            <div class="app-header__left">
                <button
                    class="app-header__toggle"
                    aria-label="Alternar menú lateral"
                    @click="toggleSidebar"
                >
                    <i class="fas fa-bars"></i>
                </button>
                <Link :href="route('dashboard')" class="app-header__brand">
                    <img :src="brandLogo" alt="SIMJ Logo" class="brand-icon" />
                    <span class="brand-name">SIMJ Panel</span>
                </Link>
            </div>
            <div class="app-header__right">
                <div class="dropdown">
                    <button
                        class="app-user-toggle"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i class="fas fa-user-circle"></i>
                        <span>{{ page.props.auth.user.name }}</span>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <Link
                                class="dropdown-item"
                                :href="route('profile.edit')"
                            >
                                <i class="fas fa-user me-2"></i> Perfil
                            </Link>
                        </li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <Link
                                class="dropdown-item"
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Cerrar sesión
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="app-body">
            <aside class="app-sidebar">
                <div class="app-sidebar__brand">Panel de control</div>
                <nav class="sidebar-nav">
                    <Link
                        :href="route('dashboard')"
                        class="sidebar-link"
                        :class="{ active: route().current('dashboard') }"
                    >
                        <i class="fas fa-home"></i>
                        <span>Inicio</span>
                    </Link>
                    <Link
                        :href="route('tasks.index')"
                        class="sidebar-link"
                        :class="{ active: route().current('tasks.*') }"
                    >
                        <i class="fas fa-calendar-alt"></i>
                        <span>Control Proyectos</span>
                    </Link>
                    <Link
                        v-if="page.props.auth.user.is_admin"
                        :href="route('users.index')"
                        class="sidebar-link"
                        :class="{ active: route().current('users.*') }"
                    >
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </Link>
                </nav>
            </aside>

            <main class="app-main">
                <section class="page-header">
                    <div>
                        <h1 class="page-title">
                            <slot name="header">Panel Principal</slot>
                        </h1>
                        <p class="page-subtitle">
                            Gestiona proyectos, tareas e informes desde un único
                            tablero.
                        </p>
                    </div>
                </section>
                <section class="page-content">
                    <slot></slot>
                </section>
            </main>
        </div>

        <footer class="app-footer">
            <div>
                <strong>&copy; 2026 SIMJ Panel.</strong> Todos los derechos
                reservados.
            </div>
            <div class="footer-meta">
                Versión <span class="fw-bold">1.0.0</span>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.app-shell {
    min-height: 100vh;
    background: #f4f6f9;
    display: flex;
    flex-direction: column;
}

.app-shell--collapsed .app-sidebar {
    width: 70px;
}

.app-shell--collapsed .sidebar-link span {
    display: none;
}

.app-shell--collapsed .app-sidebar__brand {
    text-align: center;
    padding-left: 0;
    letter-spacing: normal;
}

.app-shell--collapsed .app-main {
    margin-left: 0;
}

.app-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1.5rem;
    background: #fff;
    border-bottom: 1px solid #e4e7ee;
    box-shadow: 0 1px 8px rgba(15, 23, 42, 0.08);
    position: sticky;
    top: 0;
    z-index: 10;
}

.app-header__left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.app-header__toggle {
    border: none;
    background: transparent;
    font-size: 1.2rem;
    color: #1f2a37;
    cursor: pointer;
}

.app-header__brand {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    color: #1f2a37;
    font-weight: 600;
    font-size: 1rem;
}

.brand-icon {
    border-radius: 50%;
    width: 42px;
    height: 42px;
    object-fit: cover;
}

.app-header__right .app-user-toggle {
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: #1f2a37;
    font-weight: 500;
    cursor: pointer;
}

.app-body {
    flex: 1;
    display: flex;
    overflow: hidden;
}

.app-sidebar {
    width: 240px;
    background: #1e2737;
    color: #fff;
    display: flex;
    flex-direction: column;
    transition: width 0.2s ease;
}

.app-sidebar__brand {
    padding: 1.3rem 1.5rem;
    font-size: 0.85rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    font-weight: 600;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.8);
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    padding: 0.8rem 1rem;
}

.sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    color: rgba(255, 255, 255, 0.9);
    padding: 0.85rem 1rem;
    border-radius: 0.8rem;
    font-weight: 500;
    transition:
        background 0.2s ease,
        transform 0.2s ease;
}

.sidebar-link:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(2px);
}

.sidebar-link.active {
    background: #fff;
    color: #1f2a37;
}

.app-main {
    flex: 1;
    padding: 1.8rem 2rem 2.4rem;
    overflow-y: auto;
}

.page-header {
    margin-bottom: 1.2rem;
}

.page-title {
    font-weight: 600;
    color: #1f2a37;
    margin-bottom: 0.3rem;
}

.page-subtitle {
    color: #536072;
    font-size: 0.95rem;
    margin: 0;
}

.page-content {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 35px rgba(15, 23, 42, 0.08);
    padding: 1.8rem;
}

.app-footer {
    border-top: 1px solid #e4e7ee;
    padding: 0.8rem 1.5rem;
    display: flex;
    justify-content: space-between;
    background: #fff;
    font-size: 0.9rem;
}

.footer-meta {
    color: #667085;
}
</style>
