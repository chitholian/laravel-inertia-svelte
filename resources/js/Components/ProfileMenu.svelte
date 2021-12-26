<script>
    import {PREFIX} from "../extra";

    export let user = {}
</script>
<div class="dropdown on-hover">
    <i class="fas fa-user"></i> { user.username }
    <div class="dropdown-menu dropdown-menu-right">
        <a href="{PREFIX}profile/change-password" class="dropdown-item"><i class="fas fa-user-edit"></i>
            Change Password
        </a>
        <a class="dropdown-item" href="{PREFIX}logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>
