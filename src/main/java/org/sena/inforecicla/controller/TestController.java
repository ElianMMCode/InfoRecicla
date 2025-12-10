package org.sena.inforecicla.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;
@Controller
public class TestController {
    
    @GetMapping("/test-registros")
    public String testRegistros() {
        return "registro-test";
    }
}

