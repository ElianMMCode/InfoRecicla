package org.sena.inforecicla.config;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.context.event.ApplicationReadyEvent;
import org.springframework.context.event.EventListener;
import org.springframework.core.io.ClassPathResource;
import org.springframework.jdbc.datasource.init.ResourceDatabasePopulator;
import org.springframework.stereotype.Component;

import javax.sql.DataSource;
import java.io.IOException;

@Component
public class DataInitializer {
    @Autowired
    private DataSource dataSource;

    @EventListener(ApplicationReadyEvent.class)
    public void initializeData() throws IOException {
        try {
            ResourceDatabasePopulator populator = new ResourceDatabasePopulator();
            populator.addScript(new ClassPathResource("data.sql"));
            populator.setContinueOnError(true);
            populator.setSeparator(";");
            populator.execute(dataSource);
            System.out.println("✓ Data initialization completed successfully");
        } catch (Exception e) {
            System.err.println("✗ Error during data initialization: " + e.getMessage());
            e.printStackTrace();
        }
    }
}

