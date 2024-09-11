import {createRouter, createWebHistory} from "vue-router";

console.log("hola buenas");

// importar middleware
// import hasPermission from "../middleware/hasPermission";

import ExampleComponent from "../components/ExampleComponent";
import LoginComponent from "../components/loginComponent.vue";
import PrincipalComponent from "../components/principalComponent.vue";
import RecipeComponent from "../components/recipeComponent.vue";
import RegisterComponent from "../components/registerComponent.vue";
import UpComponent from "../components/upComponent.vue";
import FavoriteComponent from "../components/favoriteComponent.vue";
import ProfileComponent from "../components/profileComponent.vue";
import SettingsComponent from "../components/settingsComponent.vue";

const routes = [
    {
        path: '/',
        name: 'login',
        meta: {
            title: '',
            group: ''
        },
        component: LoginComponent,
    },{
        path: '/home',
        name: 'principal',
        meta: {
            title: '',
            group: '',
            requiresAuth: true
        },
        component: PrincipalComponent,
    },{
        path: '/recipe',
        name: 'recipe',
        meta: {
            title: '',
            group: '',
            requiresAuth: true
        },
        component: RecipeComponent,
    },{
        path: '/register',
        name: 'register',
        meta: {
            title: '',
            group: ''
        },
        component: RegisterComponent,
    },{
        path: '/up',
        name: 'up',
        meta: {
            title: '',
            group: '',
            requiresAuth: true
        },
        component: UpComponent,
    },{
        path: '/favorite',
        name: 'favorite',
        meta: {
            title: '',
            group: '',
            requiresAuth: true
        },
        component: FavoriteComponent,
    },{
        path: '/profile',
        name: 'profile',
        meta: {
            title: '',
            group: '',
            requiresAuth: true
        },
        component: ProfileComponent,
    },{
        path: '/admin',
        name: 'settings',
        meta: {
            title: '',
            group: '',
            requiresAuth: true
        },
        component: SettingsComponent,
    },
]

// =====================================================================================================================

const router = createRouter({
    history: createWebHistory(),
    mode: 'history',
    routes: routes
});

function nextFactory(context, middleware, index) {
    const subsequentMiddleware = middleware[index];
    if (!subsequentMiddleware) return context.next;

    return (...parameters) => {
        context.next(...parameters);
        const nextMiddleware = nextFactory(context, middleware, index + 1);
        subsequentMiddleware({...context, next: nextMiddleware});
    };
}

router.beforeEach((to, from, next) => {
    let parts = (document.title).split(" | ");

    if (parts.length > 1) document.title = `${parts[0]} | ${to.meta.title}`; else document.title = to.meta.title;

    if (to.meta.middleware) {
        const middleware = Array.isArray(to.meta.middleware) ? to.meta.middleware : [to.meta.middleware];
        const context = {
            from,
            next,
            router,
            to,
        };

        const nextMiddleware = nextFactory(context, middleware, 1);
        return middleware[0]({...context, next: nextMiddleware});
    }

    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!localStorage.getItem('user')) {
            next('/');
        }
        else {
            next();
        }
    } else {
        next();
    }

    // next();
})


export default router;
