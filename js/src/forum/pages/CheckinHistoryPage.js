import UserPage from 'flarum/forum/components/UserPage';
import dynamicallyLoadLib from "../utils/dynamicallyLoadLib";
import SupplementCheckinModal from "../modal/SupplementCheckinModal";

export default class CheckinHistoryPage extends UserPage {

  // calendar = null;

  // 初始化时候，方便放一些准备用的数据，或者用来网络请求。此时可以拿到 vnode，但是不一定拿得到真实 DOM，所以这里不推荐进行相关的 DOM 操作，比如：vnode.dom。
  oninit(vnode) {
    super.oninit(vnode);

    this.loadUser(m.route.param('username'));
  }

  content() {
    if (app.session.user) {
      return (
        <div className="CheckinHistoryUserPage">
          <div id="calendar" />
        </div>
      );
    }
  }

  // 创建成功，此时可以拿到真实 DOM 了。
  oncreate(vnode) {
    this.renderCalendar(vnode);
  }

  // DOM 渲染刷新后。业务有刷新变动数据时候使用。
  onupdate() {

  }

  // DOM 销毁前。常用，比如我们的离开动画。
  onbeforeremove() {

  }

  // DOM 渲染刷新前。业务有刷新变动数据时候使用。
  onremove() {

  }

  // DOM 渲染刷新前。业务有刷新变动数据时候使用。
  onbeforeupdate() {

  }

  async getData(info, successCb, failureCb) {
    console.log(this.user.id())
    this.history = await app.store.find('checkin/history', {
      start: info.start.toISOString(),
      end: info.end.toISOString(),
      username: this.user.slug(),
      userId: this.user.id()
    });

    return this.history.payload.data.map((item) => {
      return item.attributes;
    });
  }

  async renderCalendar(vnode) {
    await dynamicallyLoadLib('fullcalendarCore');
    await dynamicallyLoadLib(['fullcalendarLocales', 'fullcalendarDayGrid', 'fullcalendarInteraction', 'fullcalendarList']);

    const calendarEl = document.getElementById('calendar');
    const openModal = await this.openCreateModal.bind(this);

    this.calendar = new FullCalendar.Calendar(calendarEl, {
      locale: app.translator.getLocale(),
      allDayText: "今天",
      initialView: 'dayGridMonth',
      dateClick: function (info) {
        openModal(info);
      },
      events: (info, successCb, failureCb) => this.getData(info, successCb, failureCb)
    });
    this.calendar.render();
  }

  async openCreateModal(info) {
    const refetchEvents = this.calendar.refetchEvents.bind(this.calendar);

    if (new Date(info.dateStr).getTime() > new Date()) {
      return false;
    }

    const list = this.history.payload.data;
    for (var index in list) {
      if (list[index].attributes.start == info.dateStr) {
        return false;
      }
    }

    app.modal.show(SupplementCheckinModal, {info, callback: () => {
      this.getData(this.calendar.currentData.dateProfile.activeRange, null, null);
      this.calendar.refetchEvents()
    }})
  }

}
