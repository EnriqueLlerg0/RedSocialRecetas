export default function hasPermission({next, to}) {
    let user = JSON.parse(localStorage.getItem('selectedUser'))

    if (!user.permissions.includes(to.meta.permission))
        return next();

    return next();
}
