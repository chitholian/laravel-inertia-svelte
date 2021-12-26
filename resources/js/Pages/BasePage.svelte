<script>
    import Menu from "../Components/Menu.svelte";
    import {page} from "@inertiajs/inertia-svelte";
    import Alert from "../Components/Alert.svelte";

    let msg
    $: msg = $page.props.message

</script>

<header>
    <Menu server_conf={$page.props.server_conf}/>
</header>
<main>
    {#if msg}
        <div style="position: fixed;top: 0; right: 0;z-index: 999999">
            <Alert data={msg} on:dismiss={() => msg = null}>{msg.m}</Alert>
        </div>
    {/if}
    <div class="clearfix"></div>
    <slot></slot>
</main>
<footer>
    <div class="version-info">
        <strong class="text-monospace">{$page.props.server_conf.version}</strong> - Developed by
        <a href="http://f1domain.com" target="_blank">F1Domain <i class="fas fa-external-link-square-alt"></i></a>
    </div>
</footer>
