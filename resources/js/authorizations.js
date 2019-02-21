let user = window.App.user;

module.exports = {
    owns(model, prop = 'user_id') {
        return model[prop] === user.id;
    },

    isAdmin() {
        // return ['LiangYu', 'Kevin'].includes(user.name);
        return user.isAdmin;
    }
};
