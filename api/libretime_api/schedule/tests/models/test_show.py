from ...models import Show


def test_show_live_enabled():
    show = Show(
        name="My Test Show",
        description="My test show description",
    )
    assert not show.live_enabled

    show.live_auth_registered = True
    assert show.live_enabled

    show.live_auth_custom = True
    assert show.live_enabled
