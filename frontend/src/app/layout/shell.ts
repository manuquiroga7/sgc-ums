import { Component, OnInit, inject } from '@angular/core';
import {
  ActivatedRoute,
  NavigationEnd,
  Router,
  RouterLink,
  RouterLinkActive,
  RouterOutlet,
} from '@angular/router';
import { filter } from 'rxjs';
import { AuthService } from '../core/auth.service';
import { PageTitleService } from '../core/page-title.service';

@Component({
  selector: 'app-shell',
  imports: [RouterOutlet, RouterLink, RouterLinkActive],
  templateUrl: './shell.html',
})
export class Shell implements OnInit {
  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);
  private readonly route = inject(ActivatedRoute);
  private readonly pageTitleSvc = inject(PageTitleService);

  readonly pageTitle = this.pageTitleSvc.title;

  ngOnInit(): void {
    this.updateTitle();
    this.router.events
      .pipe(filter((e) => e instanceof NavigationEnd))
      .subscribe(() => this.updateTitle());
  }

  private updateTitle(): void {
    let r = this.route.firstChild;
    while (r?.firstChild) {
      r = r.firstChild;
    }
    this.pageTitleSvc.set(r?.snapshot.data?.['title'] ?? 'Página Principal');
  }

  logout(): void {
    this.auth.logout();
  }
}
